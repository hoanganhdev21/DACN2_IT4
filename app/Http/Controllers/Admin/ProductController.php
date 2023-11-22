<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\Producer;
use App\Models\Promotion;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\OrderDetail;
// Controller sản phẩm:
class ProductController extends Controller
{
  
  /**
   * Hàm này lấy danh sách các sản phẩm có thông tin chi tiết nhất định và lọc ra những sản phẩm không có
   * nhập số lượng và đưa chúng về chế độ xem để hiển thị.
   * 
   * @return Hàm này đang trả về một bộ sưu tập các sản phẩm với nhà sản xuất được liên kết của chúng và một
   * đếm chi tiết sản phẩm có số lượng nhập lớn hơn 0 và số lượng lớn hơn 0.
   * sản phẩm được sắp xếp theo ngày tạo theo thứ tự giảm dần.
   */
  public function index()
  {
    $products = Product::select('id', 'producer_id', 'name', 'image', 'sku_code', 'OS', 'rate', 'created_at')
    ->whereHas('product_details', function (Builder $query) {
      $query->where('import_quantity', '>', 0);
    })
    ->with([
      'producer' => function ($query) {
        $query->select('id', 'name');
      }
    ])
    ->withCount([
    'product_details' => function (Builder $query) {
        $query->where([['import_quantity', '>', 0], ['quantity', '>', 0]]);
      }
    ])->latest()->get();
    return view('admin.product.index')->with('products', $products);
  }

  
  /**
   * Chức năng này xóa một sản phẩm và các chi tiết liên quan, khuyến mãi và bình chọn nếu nó đáp ứng
   * điều kiện nhất định và trả về thông báo lỗi hoặc thành công.
   * 
   * @param Request yêu cầu Tham số là một thể hiện của lớp Illuminate\Http\Request,
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như HTTP
   * phương thức, tiêu đề và dữ liệu đầu vào. Trong chức năng này, nó được sử dụng để truy xuất ID sản phẩm
   * cần phải được xóa.
   * 
   * @return một Phản hồi JSON với biến dữ liệu chứa loại, tiêu đề và nội dung của
   * tin nhắn.
   */
  public function delete(Request $request)
  {
    $product = Product::whereHas('product_details', function (Builder $query) {
      $query->where('import_quantity', '>', 0);
    })->where('id', $request->product_id)->first();

    if(!$product) {

      $data['type'] = 'error';
      $data['title'] = 'Thất Bại';
      $data['content'] = 'Bạn không thể xóa sản phẩm không tồn tại!';
    } else {

      $can_delete = 1;
      $product_details = $product->product_details;
      foreach($product_details as $product_detail) {
        if($product_detail->import_quantity == 0 || $product_detail->import_quantity != $product_detail->quantity) {
          $can_delete = 0;
          break;
        }
      }

      if($can_delete) {

        foreach($product_details as $product_detail) {
          foreach($product_detail->product_images as $image) {
            Storage::disk('public')->delete('images/products/' . $image->image_name);
            $image->delete();
          }
          $product_detail->delete();
        }
        foreach ($product->promotions as $promotion) {
          $promotion->delete();
        }
        foreach ($product->product_votes as $product_vote) {
          $product_vote->delete();
        }
        $product->delete();
      } else {
        foreach($product_details as $product_detail) {
          if($product_detail->import_quantity > 0 && $product_detail->import_quantity == $product_detail->quantity) {

            foreach($product_detail->product_images as $image) {
              Storage::disk('public')->delete('images/products/' . $image->image_name);
              $image->delete();
            }
            $product_detail->delete();
          } else {

            $product_detail->import_quantity = 0;
            $product_detail->quantity = 0;
            $product_detail->save();
          }
        }
        foreach ($product->promotions as $promotion) {
          $promotion->delete();
        }
      }

      $data['type'] = 'success';
      $data['title'] = 'Thành Công';
      $data['content'] = 'Xóa sản phẩm thành công!';
    }

    return response()->json($data, 200);
  }

  
  /**
   * Hàm PHP này truy xuất danh sách các nhà sản xuất và chuyển nó tới dạng xem để hiển thị biểu mẫu tới
   * tạo sản phẩm mới.
   * 
   * @param Request yêu cầu là một thể hiện của lớp Yêu cầu chứa dữ liệu được gửi
   * bởi máy khách trong yêu cầu HTTP. Nó có thể được sử dụng để truy xuất dữ liệu đầu vào, tệp, cookie, tiêu đề,
   * và các thông tin khác liên quan đến yêu cầu. Trong trường hợp này, nó được sử dụng để lấy dữ liệu cần thiết
   * để hiển thị cái mới
   * 
   * @return một chế độ xem có tên "admin.product.new" và chuyển một biến có tên "nhà sản xuất" cho chế độ xem đó.
   * chứa một tập hợp tất cả các nhà sản xuất được sắp xếp theo tên theo thứ tự tăng dần.
   */
  public function new(Request $request)
  {
    $producers = Producer::select('id', 'name')->orderBy('name', 'asc')->get();
    return view('admin.product.new')->with('producers', $producers);
  }

  /**
   * Chức năng lưu một sản phẩm mới với các chi tiết, khuyến mãi và hình ảnh của nó vào cơ sở dữ liệu.
   * 
   * @param Request yêu cầu là một thể hiện của lớp Illuminate\Http\Request, mà
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như phương thức HTTP,
   * tiêu đề và dữ liệu đầu vào. Trong hàm này dùng để lấy dữ liệu đầu vào từ form
   * trình và xử lý tải lên tập tin.
   * 
   * @return chuyển hướng đến trang chỉ mục sản phẩm của quản trị viên với thông báo cảnh báo cho biết rằng
   * sản phẩm đã được thêm thành công.
   */
  public function save(Request $request)
  {
    $product = new Product;

    if($request->information_details != null) {
      //Xử lý Ảnh trong nội dung
      $information_details = $request->information_details;

      $dom = new \DomDocument();

      // conver utf-8 to html entities
      $information_details = mb_convert_encoding($information_details, 'HTML-ENTITIES', "UTF-8");

      $dom->loadHtml($information_details, LIBXML_HTML_NODEFDTD);

      $images = $dom->getElementsByTagName('img');

      foreach($images as $k => $img){

          $data = $img->getAttribute('src');

          if(Str::containsAll($data, ['data:image', 'base64'])){

              list(, $type) = explode('data:image/', $data);
              list($type, ) = explode(';base64,', $type);

              list(, $data) = explode(';base64,', $data);

              $data = base64_decode($data);

              $image_name = time().$k.'_'.Str::random(8).'.'.$type;

              Storage::disk('public')->put('images/posts/'.$image_name, $data);

              $img->removeAttribute('src');
              $img->setAttribute('src', '/storage/images/posts/'.$image_name);
          }
      }

      $information_details = $dom->saveHTML();

      //conver html-entities to utf-8
      $information_details = mb_convert_encoding($information_details, "UTF-8", 'HTML-ENTITIES');

      //get content
      list(, $information_details) = explode('<html><body>', $information_details);
      list($information_details, ) = explode('</body></html>', $information_details);

      $product->information_details = $information_details;
    }
    if($request->product_introduction != null) {
      //Xử lý Ảnh trong nội dung
      $product_introduction = $request->product_introduction;

      $dom = new \DomDocument();

      // conver utf-8 to html entities
      $product_introduction = mb_convert_encoding($product_introduction, 'HTML-ENTITIES', "UTF-8");

      $dom->loadHtml($product_introduction, LIBXML_HTML_NODEFDTD);

      $images = $dom->getElementsByTagName('img');

      foreach($images as $k => $img){

          $data = $img->getAttribute('src');

          if(Str::containsAll($data, ['data:image', 'base64'])){

              list(, $type) = explode('data:image/', $data);
              list($type, ) = explode(';base64,', $type);

              list(, $data) = explode(';base64,', $data);

              $data = base64_decode($data);

              $image_name = time().$k.'_'.Str::random(8).'.'.$type;

              Storage::disk('public')->put('images/posts/'.$image_name, $data);

              $img->removeAttribute('src');
              $img->setAttribute('src', '/storage/images/posts/'.$image_name);
          }
      }

      $product_introduction = $dom->saveHTML();

      //conver html-entities to utf-8
      $product_introduction = mb_convert_encoding($product_introduction, "UTF-8", 'HTML-ENTITIES');

      //get content
      list(, $product_introduction) = explode('<html><body>', $product_introduction);
      list($product_introduction, ) = explode('</body></html>', $product_introduction);

      $product->product_introduction = $product_introduction;
    }

    $product->name = $request->name;
    $product->producer_id = $request->producer_id;
    $product->sku_code = $request->sku_code;
    $product->monitor = $request->monitor;
    $product->front_camera = $request->front_camera;
    $product->rear_camera = $request->rear_camera;
    $product->CPU = $request->CPU;
    $product->GPU = $request->GPU;
    $product->RAM = $request->RAM;
    $product->ROM = $request->ROM;
    $product->OS = $request->OS;
    $product->pin = $request->pin;
    $product->rate = 5.0;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.Str::random(8).'_'.$image->getClientOriginalName();
      $image->storeAs('images/products',$image_name,'public');
      $product->image = $image_name;
    }

    $product->save();

    if ($request->has('product_promotions')) {
      foreach ($request->product_promotions as $product_promotion) {
        $promotion = new Promotion;
        $promotion->product_id = $product->id;
        $promotion->content = $product_promotion['content'];

        //Xử lý ngày bắt đầu, ngày kết thúc
        list($start_date, $end_date) = explode(' - ', $product_promotion['promotion_date']);

        $start_date = str_replace('/', '-', $start_date);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $end_date);
        $end_date = date('Y-m-d', strtotime($end_date));

        $promotion->start_date = $start_date;
        $promotion->end_date = $end_date;

        $promotion->save();
      }
    }

    if ($request->has('product_details')) {
      foreach ($request->product_details as $key => $product_detail) {
        $new_product_detail = new ProductDetail;
        $new_product_detail->product_id = $product->id;
        $new_product_detail->color = $product_detail['color'];
        $new_product_detail->import_quantity = $product_detail['quantity'];
        $new_product_detail->quantity = $product_detail['quantity'];
        $new_product_detail->import_price = str_replace('.', '', $product_detail['import_price']);
        $new_product_detail->sale_price = str_replace('.', '', $product_detail['sale_price']);
        if($product_detail['promotion_price'] != null) {
          $new_product_detail->promotion_price = str_replace('.', '', $product_detail['promotion_price']);
        }
        if($product_detail['promotion_date'] != null) {
          //Xử lý ngày bắt đầu, ngày kết thúc
          list($start_date, $end_date) = explode(' - ', $product_detail['promotion_date']);

          $start_date = str_replace('/', '-', $start_date);
          $start_date = date('Y-m-d', strtotime($start_date));

          $end_date = str_replace('/', '-', $end_date);
          $end_date = date('Y-m-d', strtotime($end_date));

          $new_product_detail->promotion_start_date = $start_date;
          $new_product_detail->promotion_end_date = $end_date;
        }

        $new_product_detail->save();

        foreach ($request->file('product_details')[$key]['images'] as $image) {
          $image_name = time().'_'.Str::random(8).'_'.$image->getClientOriginalName();
          $image->storeAs('images/products',$image_name,'public');

          $new_image = new ProductImage;
          $new_image->product_detail_id = $new_product_detail->id;
          $new_image->image_name = $image_name;

          $new_image->save();
        }
      }
    }

    return redirect()->route('admin.product.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Thêm sản phẩm thành công.'
    ]]);
  }


  /**
   * Chức năng này truy xuất và hiển thị thông tin về một sản phẩm cụ thể cho mục đích chỉnh sửa,
   * bao gồm nhà sản xuất, thông tin chi tiết, chương trình khuyến mãi và hình ảnh của nó.
   * 
   * @param id ID của sản phẩm cần chỉnh sửa.
   * 
   * @return Hàm này đang trả về chế độ xem có tên 'admin.product.edit' với dữ liệu của sản phẩm
   * và nhà sản xuất.
   */
  public function edit($id)
  {
    $producers = Producer::select('id', 'name')->orderBy('name', 'asc')->get();
    $product = Product::select('id', 'producer_id', 'name', 'image', 'sku_code', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'information_details', 'product_introduction')
    ->whereHas('product_details', function (Builder $query) {
      $query->where('import_quantity', '>', 0);
    })->where('id', $id)->with([
      'promotions' => function ($query) {
        $query->select('id', 'product_id', 'content', 'start_date', 'end_date');
      },
      'product_details' => function ($query) {
        $query->select('id', 'product_id', 'color', 'import_quantity', 'import_price', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('import_quantity', '>', 0)
        ->with([
          'product_images' => function ($query) {
            $query->select('id', 'product_detail_id', 'image_name');
          },
          'order_details' => function ($query) {
            $query->select('id', 'product_detail_id', 'quantity');
          }
        ]);
      }
    ])->first();
    if(!$product) abort(404);
    return view('admin.product.edit')->with(['product' => $product, 'producers' =>$producers]);
  }

  
  /**
   * Chức năng này cập nhật một sản phẩm trong cơ sở dữ liệu với thông tin được cung cấp trong một yêu cầu, bao gồm
   * xử lý hình ảnh và khuyến mãi.
   * 
   * @param Request yêu cầu là một thể hiện của lớp Illuminate\Http\Request, mà
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như phương thức HTTP,
   * tiêu đề và dữ liệu đầu vào. Trong hàm này dùng để lấy dữ liệu đầu vào từ form
   * do người dùng gửi, chẳng hạn như tên sản phẩm,
   * @param id ID của sản phẩm cần được cập nhật.
   * 
   * @return chuyển hướng đến trang chỉ mục sản phẩm của quản trị viên với thông báo cảnh báo thành công.
   */
  public function update(Request $request, $id) {

    $product = Product::whereHas('product_details', function (Builder $query) {
      $query->where('import_quantity', '>', 0);
    })->where('id', $id)->first();
    if(!$product) abort(404);

    if($request->information_details != null) {
      //Xử lý Ảnh trong nội dung
      $information_details = $request->information_details;

      $dom = new \DomDocument();

      // conver utf-8 to html entities
      $information_details = mb_convert_encoding($information_details, 'HTML-ENTITIES', "UTF-8");

      $dom->loadHtml($information_details, LIBXML_HTML_NODEFDTD);

      $images = $dom->getElementsByTagName('img');

      foreach($images as $k => $img){

          $data = $img->getAttribute('src');

          if(Str::containsAll($data, ['data:image', 'base64'])){

              list(, $type) = explode('data:image/', $data);
              list($type, ) = explode(';base64,', $type);

              list(, $data) = explode(';base64,', $data);

              $data = base64_decode($data);

              $image_name = time().$k.'_'.Str::random(8).'.'.$type;

              Storage::disk('public')->put('images/posts/'.$image_name, $data);

              $img->removeAttribute('src');
              $img->setAttribute('src', '/storage/images/posts/'.$image_name);
          }
      }

      $information_details = $dom->saveHTML();

      //conver html-entities to utf-8
      $information_details = mb_convert_encoding($information_details, "UTF-8", 'HTML-ENTITIES');

      //get content
      list(, $information_details) = explode('<html><body>', $information_details);
      list($information_details, ) = explode('</body></html>', $information_details);

      $product->information_details = $information_details;
    }
    if($request->product_introduction != null) {
      //Xử lý Ảnh trong nội dung
      $product_introduction = $request->product_introduction;

      $dom = new \DomDocument();

      // conver utf-8 to html entities
      $product_introduction = mb_convert_encoding($product_introduction, 'HTML-ENTITIES', "UTF-8");

      $dom->loadHtml($product_introduction, LIBXML_HTML_NODEFDTD);

      $images = $dom->getElementsByTagName('img');

      foreach($images as $k => $img){

          $data = $img->getAttribute('src');

          if(Str::containsAll($data, ['data:image', 'base64'])){

              list(, $type) = explode('data:image/', $data);
              list($type, ) = explode(';base64,', $type);

              list(, $data) = explode(';base64,', $data);

              $data = base64_decode($data);

              $image_name = time().$k.'_'.Str::random(8).'.'.$type;

              Storage::disk('public')->put('images/posts/'.$image_name, $data);

              $img->removeAttribute('src');
              $img->setAttribute('src', '/storage/images/posts/'.$image_name);
          }
      }

      $product_introduction = $dom->saveHTML();

      //conver html-entities to utf-8
      $product_introduction = mb_convert_encoding($product_introduction, "UTF-8", 'HTML-ENTITIES');

      //get content
      list(, $product_introduction) = explode('<html><body>', $product_introduction);
      list($product_introduction, ) = explode('</body></html>', $product_introduction);

      $product->product_introduction = $product_introduction;
    }

    $product->name = $request->name;
    $product->producer_id = $request->producer_id;
    $product->sku_code = $request->sku_code;
    $product->monitor = $request->monitor;
    $product->front_camera = $request->front_camera;
    $product->rear_camera = $request->rear_camera;
    $product->CPU = $request->CPU;
    $product->GPU = $request->GPU;
    $product->RAM = $request->RAM;
    $product->ROM = $request->ROM;
    $product->OS = $request->OS;
    $product->pin = $request->pin;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.Str::random(8).'_'.$image->getClientOriginalName();
      $image->storeAs('images/products',$image_name,'public');
      Storage::disk('public')->delete('images/products/' . $product->image);
      $product->image = $image_name;
    }

    $product->save();

    if ($request->has('old_product_promotions')) {
      foreach ($request->old_product_promotions as $key => $old_product_promotion) {
        $promotion = Promotion::where('id', $key)->first();
        if(!$promotion) abort(404);

        $promotion->content = $old_product_promotion['content'];

        //Xử lý ngày bắt đầu, ngày kết thúc
        list($start_date, $end_date) = explode(' - ', $old_product_promotion['promotion_date']);

        $start_date = str_replace('/', '-', $start_date);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $end_date);
        $end_date = date('Y-m-d', strtotime($end_date));

        $promotion->start_date = $start_date;
        $promotion->end_date = $end_date;

        $promotion->save();
      }
    }

    if ($request->has('product_promotions')) {
      foreach ($request->product_promotions as $product_promotion) {
        $promotion = new Promotion;
        $promotion->product_id = $product->id;
        $promotion->content = $product_promotion['content'];

        //Xử lý ngày bắt đầu, ngày kết thúc
        list($start_date, $end_date) = explode(' - ', $product_promotion['promotion_date']);

        $start_date = str_replace('/', '-', $start_date);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $end_date);
        $end_date = date('Y-m-d', strtotime($end_date));

        $promotion->start_date = $start_date;
        $promotion->end_date = $end_date;

        $promotion->save();
      }
    }

    if ($request->has('old_product_details')) {
      foreach ($request->old_product_details as $key => $product_detail) {
        $sum = OrderDetail::where('product_detail_id', $key)->sum('quantity');
        $old_product_detail = ProductDetail::where('id', $key)->first();
        if(!$old_product_detail) abort(404);

        $old_product_detail->color = $product_detail['color'];
        $old_product_detail->import_quantity = $product_detail['quantity'];
        $old_product_detail->quantity = $product_detail['quantity'] - $sum;
        $old_product_detail->import_price = str_replace('.', '', $product_detail['import_price']);
        $old_product_detail->sale_price = str_replace('.', '', $product_detail['sale_price']);
        if($product_detail['promotion_price'] != null) {
          $old_product_detail->promotion_price = str_replace('.', '', $product_detail['promotion_price']);
        }
        if($product_detail['promotion_date'] != null) {
          //Xử lý ngày bắt đầu, ngày kết thúc
          list($start_date, $end_date) = explode(' - ', $product_detail['promotion_date']);

          $start_date = str_replace('/', '-', $start_date);
          $start_date = date('Y-m-d', strtotime($start_date));

          $end_date = str_replace('/', '-', $end_date);
          $end_date = date('Y-m-d', strtotime($end_date));

          $old_product_detail->promotion_start_date = $start_date;
          $old_product_detail->promotion_end_date = $end_date;
        }

        $old_product_detail->save();
      }
    }

    if ($request->has('product_details')) {
      foreach ($request->product_details as $key => $product_detail) {
        $new_product_detail = new ProductDetail;
        $new_product_detail->product_id = $product->id;
        $new_product_detail->color = $product_detail['color'];
        $new_product_detail->import_quantity = $product_detail['quantity'];
        $new_product_detail->quantity = $product_detail['quantity'];
        $new_product_detail->import_price = str_replace('.', '', $product_detail['import_price']);
        $new_product_detail->sale_price = str_replace('.', '', $product_detail['sale_price']);
        if($product_detail['promotion_price'] != null) {
          $new_product_detail->promotion_price = str_replace('.', '', $product_detail['promotion_price']);
        }
        if($product_detail['promotion_date'] != null) {
          //Xử lý ngày bắt đầu, ngày kết thúc
          list($start_date, $end_date) = explode(' - ', $product_detail['promotion_date']);

          $start_date = str_replace('/', '-', $start_date);
          $start_date = date('Y-m-d', strtotime($start_date));

          $end_date = str_replace('/', '-', $end_date);
          $end_date = date('Y-m-d', strtotime($end_date));

          $new_product_detail->promotion_start_date = $start_date;
          $new_product_detail->promotion_end_date = $end_date;
        }

        $new_product_detail->save();

        foreach ($request->file('product_details')[$key]['images'] as $image) {
          $image_name = time().'_'.Str::random(8).'_'.$image->getClientOriginalName();
          $image->storeAs('images/products',$image_name,'public');

          $new_image = new ProductImage;
          $new_image->product_detail_id = $new_product_detail->id;
          $new_image->image_name = $image_name;

          $new_image->save();
        }
      }
    }

    if($request->file('old_product_details') != null){
      foreach ($request->file('old_product_details') as $key => $images) {
        foreach($images['images'] as $image) {
          $image_name = time().'_'.Str::random(8).'_'.$image->getClientOriginalName();
          $image->storeAs('images/products',$image_name,'public');

          $new_image = new ProductImage;
          $new_image->product_detail_id = $key;
          $new_image->image_name = $image_name;

          $new_image->save();
        }
      }
    }

    return redirect()->route('admin.product.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Chỉnh sửa sản phẩm thành công.'
    ]]);
  }

  
  /**
   * Hàm này xóa một chương trình khuyến mãi và trả về thông báo lỗi hoặc thành công.
   * 
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa dữ liệu được gửi
   * bởi máy khách trong yêu cầu HTTP. Nó được sử dụng để truy xuất dữ liệu đầu vào, tiêu đề, cookie và các dữ liệu khác
   * thông tin liên quan đến yêu cầu. Trong chức năng này, nó được sử dụng để truy xuất promotion_id
   * tham số được gửi trong yêu cầu.
   * 
   * @return Hàm này đang trả về phản hồi JSON với thông báo lỗi hoặc thành công tùy thuộc vào
   * cho dù chương trình khuyến mãi đã được xóa thành công hay chưa. Phản hồi bao gồm một loại (thành công hoặc
   *lỗi), tiêu đề (Thành Công hoặc Thất Bại), và nội dung (Xóa khuyến mãi thành công! hoặc Bạn không thể
   * xóa khuyễn mãi không tồn tại
   */
  public function delete_promotion(Request $request)
  {
    $promotion = Promotion::where('id', $request->promotion_id)->first();

    if(!$promotion) {

      $data['type'] = 'error';
      $data['title'] = 'Thất Bại';
      $data['content'] = 'Bạn không thể xóa khuyễn mãi không tồn tại!';
    } else {

      $promotion->delete();

      $data['type'] = 'success';
      $data['title'] = 'Thành Công';
      $data['content'] = 'Xóa khuyến mãi thành công!';
    }

    return response()->json($data, 200);
  }

  
  /**
   * Chức năng này xóa chi tiết sản phẩm và hình ảnh đi kèm nếu số lượng nhập bằng nhau
   * thành số lượng, nếu không, nó đặt số lượng nhập và số lượng thành 0.
   * 
   * @param Yêu cầu yêu cầu một thể hiện của lớp Yêu cầu, chứa yêu cầu HTTP được gửi bởi
   * khách hàng.
   * 
   * @return Một phản hồi JSON với biến dữ liệu chứa loại, tiêu đề và nội dung của
   * tin nhắn và mã trạng thái là 200.
   */
  public function delete_product_detail(Request $request)
  {
    $product_detail = ProductDetail::where([['id', $request->product_detail_id], ['import_quantity', '>', 0]])->first();

    if(!$product_detail) {

      $data['type'] = 'error';
      $data['title'] = 'Thất Bại';
      $data['content'] = 'Bạn không thể xóa chi tiết sản phẩm không tồn tại!';
    } else {

      if($product_detail->import_quantity == $product_detail->quantity) {
        foreach($product_detail->product_images as $image) {
          Storage::disk('public')->delete('images/products/' . $image->image_name);
          $image->delete();
        }
        $product_detail->delete();
      } else {
        $product_detail->import_quantity = 0;
        $product_detail->quantity = 0;
        $product_detail->save();
      }

      $data['type'] = 'success';
      $data['title'] = 'Thành Công';
      $data['content'] = 'Xóa chi tiết sản phẩm thành công!';
    }

    return response()->json($data, 200);
  }

  
  /**
   * Hàm PHP này xóa hình ảnh sản phẩm và bản ghi tương ứng của nó khỏi cơ sở dữ liệu.
   * 
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa dữ liệu được gửi
   * bởi máy khách trong yêu cầu HTTP. Nó được sử dụng để lấy dữ liệu từ yêu cầu như dữ liệu biểu mẫu,
   * tham số truy vấn và tiêu đề yêu cầu. Trong chức năng cụ thể này, được sử dụng để truy xuất
   * tham số chính được sử dụng để
   * 
   *@return Một phản hồi JSON trống đang được trả về.
   */
  public function delete_image(Request $request)
  {
    $image = ProductImage::find($request->key);
    Storage::disk('public')->delete('images/products/' . $image->image_name);
    $image->delete();
    return response()->json();
  }
}
