<!DOCTYPE html>
<html>
<head>
    <title>Generated Documents</title>
</head>
<body>
    <h1>Generated Documents</h1>
    <ul>
        @foreach ($documents as $document)
            <li>
                <a href="{{ route('download', ['fileName' => basename($document)]) }}" download>
                    Download {{ basename($document) }}
                </a>
            </li>
        @endforeach
    </ul>
</body>
</html>
