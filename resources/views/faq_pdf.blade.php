<html>
    <head>
        <title>FAQ PDF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.content-pdf').html($('.content-pdf').data('description'));
            })
        </script>
    </head>

    <body>
        <h1>{{ $faq->title }}</h1>
        <span>{{ $faq->summary }}</span>
        <div>
            <h3>Detail</h3>
            <p class="content-pdf" data-description="{{$faq->description}}">{{strip_tags($faq->description)}}</p>
        </div>
    </body>
</html>