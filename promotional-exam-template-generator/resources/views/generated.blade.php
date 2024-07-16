<!DOCTYPE html>
<html>
<head>
    <title>Generated Documents</title>
</head>
<body>
    @foreach($documents as $document)
        <pre>{{ $document }}</pre>
        <hr>
    @endforeach
</body>
</html>
