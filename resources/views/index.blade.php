<html>
    <head>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body>
        <div class="container mt-5">
            <form method="POST" action="{{ route('upload')}}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="exampleFormControlFile1">請選擇要上傳的影片</label>
                    <input type="file" class="form-control-file" id="video" name="video">
                </div>
                <button type="submit">上傳</button>
            </form>
        </div>
    </body>

</html>