<!DOCTYPE html>
<html>
<head>
    <title>Template Generator</title>
</head>
<body>
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="csv">Upload CSV:</label>
        <input type="file" name="csv" id="csv" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
