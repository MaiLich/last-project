 



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Xác nhận tài khoản nhà bán hàng</title>
    </head>
    <body>
        <table>
            <tr><td>Kính gửi {{ $name }}!</td></tr>
            <tr><td>&nbsp;<br></td></tr>
            <tr><td>Vui lòng nhấp vào liên kết bên dưới để xác nhận tài khoản Nhà bán hàng của quý khách:</td></tr>
            <tr><td><a href="{{ url('vendor/confirm/' . $code) }}">{{ url('vendor/confirm/' . $code) }}</a></td></tr> 
             
            
            <tr><td>&nbsp;<br></td></tr>
            <tr><td>Trân trọng,</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>Ứng dụng Thương mại Điện tử Đa nhà bán hàng</td></tr>
        </table>
    </body>
</html>