<!DOCTYPE html>
<html>
<head>
	<title>{{ $faq->title }}</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

    <h1 class="d-flex justify-content-center">{{ $faq->title }}</h1>
    <span>{{ $faq->summary }}</span>
    <br><br>
    <div>
        <h3>Detail</h3>
        <p class="content-pdf">{!! $faq->description !!}</p>
    </div>
</body>
</html>
