<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #666;
        }

        .content {
            text-align: justify;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    <div class="date">
        Gerado em: {{ $date }}
    </div>

    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>
</body>

</html>