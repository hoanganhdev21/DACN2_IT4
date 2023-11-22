Request hiểu một các đơn giản nó là các yêu cầu mà gửi từ client đến server.
Để lấy đối tượng của HTTP request hiện tại thông qua dependency injection ta chỉ cần use Illuminate\Http\Request phần đầu của controller hay type-hint phương thức trong controller, đối tượng của request hiện tại sẽ được tự động inject vào bởi service container.
