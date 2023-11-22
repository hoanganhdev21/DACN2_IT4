<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;
// Controller bài viết:
/* Lớp PostController chứa các phương thức truy xuất, tạo, cập nhật và xóa các bài đăng trong
bảng quản trị, cũng như xử lý hình ảnh trong nội dung bài đăng. */
class PostController extends Controller
{
  /**
   * This function retrieves the latest posts from the database and returns them to the view for display.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class which represents an HTTP request. It contains information about the request such as the HTTP method, headers, and input data. In this specific function, it is not used but it is a common parameter in Laravel controller methods.
   * 
   * @return a view named 'admin.post.index' with a variable named 'posts' which contains the latest posts' id, title, image, and created_at attributes.
   */
  public function index(Request $request)
  {
    $posts = Post::select('id', 'title', 'image', 'created_at')->latest()->get();
    return view('admin.post.index')->with('posts', $posts);
  }
  /**
   * The function returns a view for creating a new post in the admin panel.
   * 
   * @param Request request  is an instance of the Request class which represents an HTTP
   * request made to the application. It contains information about the request such as the HTTP
   * method, headers, and any data sent in the request body. In this case, it is being used to render a
   * view for creating a new post in an
   * 
   * @return A view called "admin.post.new" is being returned.
   */
  /**
   * The function returns a view for creating a new post in the admin panel.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class which
   * represents an HTTP request. It contains information about the request such as the HTTP method,
   * headers, and input data. In this context, it is used to handle the request for a new post and
   * return the corresponding view.
   * 
   * @return A view called "admin.post.new" is being returned.
   */
  public function new(Request $request)
  {
    return view('admin.post.new');
  }
  /**
   * This function saves a post with a title, content, and image, and handles image processing within
   * the content.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class which
   * represents an HTTP request. It contains information about the request such as the HTTP method,
   * headers, and input data. In this function, it is used to retrieve the input data from the form
   * submission and the uploaded image file.
   * 
   * @return a redirect to the admin post index page with a success alert message.
   */
  public function save(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'content' => 'required',
      'image' => 'required',
    ], [
      'title.required' => 'Tiêu đề bài viết không được để trống!',
      'content.required' => 'Nội dung bài viết không được để trống!',
      'image.required' => 'Hình ảnh hiển thị bài viết phải được tải lên!',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    //Xử lý Ảnh trong nội dung
    $content = $request->content;

    $dom = new \DomDocument();

    // conver utf-8 to html entities
    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");

    $dom->loadHtml($content, LIBXML_HTML_NODEFDTD);

    $images = $dom->getElementsByTagName('img');

    foreach($images as $k => $img){

        $data = $img->getAttribute('src');

        if(Str::containsAll($data, ['data:image', 'base64'])){

            list(, $type) = explode('data:image/', $data);
            list($type, ) = explode(';base64,', $type);

            list(, $data) = explode(';base64,', $data);

            $data = base64_decode($data);

            $image_name= time().$k.'.'.$type;

            Storage::disk('public')->put('images/posts/'.$image_name, $data);

            $img->removeAttribute('src');
            $img->setAttribute('src', '/storage/images/posts/'.$image_name);
        }
    }

    $content = $dom->saveHTML();

    //conver html-entities to utf-8
    $content = mb_convert_encoding($content, "UTF-8", 'HTML-ENTITIES');

    //get content
    list(, $content) = explode('<html><body>', $content);
    list($content, ) = explode('</body></html>', $content);

    $post = new Post;
    $post->title = $request->title;
    $post->content = $content;
    $post->user_id = Auth::user()->id;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.$image->getClientOriginalName();
      $image->storeAs('images/posts',$image_name,'public');
      $post->image = $image_name;
    }

    $post->save();

    return redirect()->route('admin.post.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Bài viết của bạn đã được tạo thành công.'
    ]]);
  }

  /**
   * This function deletes a post and its associated image(s) from storage and returns a success or
   * error message.
   * 
   * @param Request request The  parameter is an instance of the Illuminate\Http\Request class,
   * which represents an HTTP request. It contains information about the request such as the HTTP
   * method, headers, and any data sent in the request body. In this function, it is used to retrieve
   * the post_id parameter from the request.
   * 
   * @return A JSON response with data about the success or failure of deleting a post, including a
   * type (success or error), title, and content message.
   */
  public function delete(Request $request)
  {
    $post = Post::where('id', $request->post_id)->first();

    if(!$post) {

      $data['type'] = 'error';
      $data['title'] = 'Thất Bại';
      $data['content'] = 'Bạn không thể xóa bài viết không tồn tại!';
    } else {
      Storage::disk('public')->delete('images/posts/' . $post->image);
      if($post->content != null) {
        $dom = new \DomDocument();

        // conver utf-8 to html entities
        $content = mb_convert_encoding($post->content, 'HTML-ENTITIES', "UTF-8");

        $dom->loadHtml($content, LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);

        $images = $dom->getElementsByTagName('img');

        foreach($images as $img){

            $src = $img->getAttribute('src');
            $src = mb_convert_encoding($src, "UTF-8", 'HTML-ENTITIES');

            if(Str::startsWith($src, '/storage/images/posts/')){

                list(, $src) = explode('/storage/', $src);

                Storage::disk('public')->delete($src);
            }
        }
      }

      $post->delete();

      $data['type'] = 'success';
      $data['title'] = 'Thành Công';
      $data['content'] = 'Xóa bài viết thành công!';
    }

    return response()->json($data, 200);
  }

  /**
   * This PHP function retrieves a post with a specific ID and displays it in an edit view for an admin
   * user.
   * 
   * @param id The  parameter is the unique identifier of the post that needs to be edited. It is
   * used to retrieve the post from the database using the Post model's where() method. If the post is
   * not found, a 404 error is thrown. The retrieved post is then passed to the edit view
   * 
   * @return a view called 'admin.post.edit' with a variable called 'post' which contains the Post
   * model instance with the given . If the Post model instance is not found, it will return a 404
   * error page.
   */
  public function edit($id)
  {
    $post = Post::where('id', $id)->first();
    if(!$post) abort(404);
    return view('admin.post.edit')->with('post', $post);
  }
  /**
   * This function updates a post with a new title, content, and image (if provided) and also processes
   * any images within the content by converting them to base64 and storing them in the public disk.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class which
   * represents an HTTP request. It contains information about the request such as the HTTP method,
   * headers, and input data. In this function, it is used to retrieve the input data and uploaded file
   * from the form submission, and to validate the input data
   * @param id The  parameter is the identifier of the post that needs to be updated. It is used to
   * retrieve the post from the database and update its title, content, and image (if provided).
   * 
   * @return a redirect to the admin post index page with a success alert message.
   */
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'content' => 'required',
    ], [
      'title.required' => 'Tiêu đề bài viết không được để trống!',
      'content.required' => 'Nội dung bài viết không được để trống!',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    //Xử lý Ảnh trong nội dung
    $content = $request->content;

    $dom = new \DomDocument();

    // conver utf-8 to html entities
    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");

    $dom->loadHtml($content, LIBXML_HTML_NODEFDTD);

    $images = $dom->getElementsByTagName('img');

    foreach($images as $k => $img){

        $data = $img->getAttribute('src');

        if(Str::containsAll($data, ['data:image', 'base64'])){

            list(, $type) = explode('data:image/', $data);
            list($type, ) = explode(';base64,', $type);

            list(, $data) = explode(';base64,', $data);

            $data = base64_decode($data);

            $image_name= time().$k.'.'.$type;

            Storage::disk('public')->put('images/posts/'.$image_name, $data);

            $img->removeAttribute('src');
            $img->setAttribute('src', '/storage/images/posts/'.$image_name);
        }
    }

    $content = $dom->saveHTML();

    //conver html-entities to utf-8
    $content = mb_convert_encoding($content, "UTF-8", 'HTML-ENTITIES');

    //get content
    list(, $content) = explode('<html><body>', $content);
    list($content, ) = explode('</body></html>', $content);

    $post = Post::where('id', $id)->first();
    $post->title = $request->title;
    $post->content = $content;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.$image->getClientOriginalName();
      $image->storeAs('images/posts',$image_name,'public');
      Storage::disk('public')->delete('images/posts/' . $post->image);
      $post->image = $image_name;
    }

    $post->save();

    return redirect()->route('admin.post.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Chỉnh sửa bài viết thành công.'
    ]]);
  }
}
