<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\Advertise;

/* Lớp AdvertiseController chứa các chức năng truy xuất, tạo, cập nhật và xóa
Quảng cáo các mô hình trong một ứng dụng PHP Laravel. */
class AdvertiseController extends Controller
{
  /**
   * This PHP function retrieves and returns a list of advertisements with specific fields from the database and passes them to a view for display.
   * 
   * @return a view called "admin.advertise.index" with a variable called "advertises" which contains a collection of Advertise models. The Advertise models have the attributes "id", "title", "image", "at_home_page", "start_date", "end_date", and "created_at". The collection is sorted in descending order by the "created_at" attribute.
   */
  public function index()
  {
    $advertises = Advertise::select('id', 'title', 'image', 'at_home_page', 'start_date', 'end_date', 'created_at')->latest()->get();
    return view('admin.advertise.index')->with('advertises', $advertises);
  }
  /**
   * The function returns a view for creating a new advertisement in the admin panel.
   * 
   * @return A view named "new" located in the "admin.advertise" directory is being returned.
   */
  public function new()
  {
    return view('admin.advertise.new');
  }
  /**
   * This function saves an advertisement with input data from a form, validates the input, processes the start and end dates, and stores the advertisement in the database.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class, which represents an HTTP request. It contains information about the request such as the HTTP method, headers, and input data. In this function, it is used to retrieve the input data submitted by the user through a form.
   * 
   * @return a redirect to the 'admin.advertise.index' route with a session flash data containing an alert message with a success type, title, and content.
   */
  public function save(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'at_home_page' => 'required',
      'date' => 'required',
      'image' => 'required',
    ], [
      'title.required' => 'Tiêu đề quảng cáo không được để trống!',
      'at_home_page.required' => 'Vị trí trang hiển thị phải được chọn!',
      'date.required' => 'Ngày bắt đầu và kết thúc phải được chọn!',
      'image.required' => 'Hình ảnh hiển thị bài viết phải được tải lên!',
    ]);

    /* This code block is checking if the validation fails or not. If the validation fails, it will redirect back to the previous page with the errors and the input data that the user has entered. The `withErrors()` method will store the validation errors in the session flash data, and the `withInput()` method will store the input data in the session flash data so that it can be retrieved and displayed in the form again. */
    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    //Xử lý ngày bắt đầu, ngày kết thúc
    list($start_date, $end_date) = explode(' - ', $request->date);

    $start_date = str_replace('/', '-', $start_date);
    $start_date = date('Y-m-d', strtotime($start_date));

    $end_date = str_replace('/', '-', $end_date);
    $end_date = date('Y-m-d', strtotime($end_date));

    $advertise = new Advertise;
    $advertise->title = $request->title;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.$image->getClientOriginalName();
      $image->storeAs('images/advertises',$image_name,'public');
      $advertise->image = $image_name;
    }

    $advertise->at_home_page = $request->at_home_page;
    $advertise->start_date = $start_date;
    $advertise->end_date = $end_date;

    $advertise->save();

    return redirect()->route('admin.advertise.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Quảng cáo của bạn đã được tạo thành công.'
    ]]);
  }

  /**
   * This function deletes an advertisement and its associated image from storage if it exists, and returns a success or error message.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class, which represents an HTTP request. It contains information about the request such as the HTTP method, headers, and parameters. In this function, it is used to retrieve the advertise_id parameter from the request.
   * 
   * @return A JSON response with a success or error message depending on whether the Advertise object was successfully deleted or not.
   */
  public function delete(Request $request)
  {
    $advertise = Advertise::where('id', $request->advertise_id)->first();

    if(!$advertise) {

      $data['type'] = 'error';
      $data['title'] = 'Thất Bại';
      $data['content'] = 'Bạn không thể xóa quảng cáo không tồn tại!';
    } else {
      Storage::disk('public')->delete('images/advertises/' . $advertise->image);

      $advertise->delete();

      $data['type'] = 'success';
      $data['title'] = 'Thành Công';
      $data['content'] = 'Xóa quảng cáo thành công!';
    }

    return response()->json($data, 200);
  }

  /**
   * This PHP function retrieves an Advertise model with a specific ID and returns a view for editing it.
   * 
   * @param id The  parameter is the unique identifier of the Advertise model that needs to be edited. It is used to retrieve the Advertise model from the database using the `where` method and then passed to the view as a variable named ``.
   * 
   * @return a view called "admin.advertise.edit" with a variable called "advertise" that contains the Advertise model with the specified ID.
   */
  public function edit($id)
  {
    $advertise = Advertise::where('id', $id)->first();
    if(!$advertise) abort(404);
    return view('admin.advertise.edit')->with('advertise', $advertise);
  }

  /**
   * This function updates an advertisement with the provided information and returns a success message.
   * 
   * @param Request request  is an instance of the Illuminate\Http\Request class which represents an HTTP request. It contains information about the request such as the HTTP method, headers, and input data.
   * @param id The ID of the Advertise record that needs to be updated.
   * 
   * @return a redirect to the index page for the Advertise model in the admin panel with a success alert message.
   */
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'at_home_page' => 'required',
      'date' => 'required',
    ], [
      'title.required' => 'Tiêu đề quảng cáo không được để trống!',
      'at_home_page.required' => 'Vị trí trang hiển thị phải được chọn!',
      'date.required' => 'Ngày bắt đầu và kết thúc phải được chọn!',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    //Xử lý ngày bắt đầu, ngày kết thúc
    list($start_date, $end_date) = explode(' - ', $request->date);

    $start_date = str_replace('/', '-', $start_date);
    $start_date = date('Y-m-d', strtotime($start_date));

    $end_date = str_replace('/', '-', $end_date);
    $end_date = date('Y-m-d', strtotime($end_date));

    $advertise = Advertise::where('id', $id)->first();
    $advertise->title = $request->title;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.$image->getClientOriginalName();
      $image->storeAs('images/advertises',$image_name,'public');
      Storage::disk('public')->delete('images/advertises/' . $advertise->image);
      $advertise->image = $image_name;
    }

    $advertise->at_home_page = $request->at_home_page;
    $advertise->start_date = $start_date;
    $advertise->end_date = $end_date;

    $advertise->save();

    return redirect()->route('admin.advertise.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Quảng cáo của bạn đã được cập nhật thành công.'
    ]]);
  }
}
