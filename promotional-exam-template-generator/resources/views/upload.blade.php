<!DOCTYPE html>
<html>
<head>
    <title>Template Generator</title>
    <style>
        .alert-success {
            color: green;
        }
        .alert-errors {
            color: red;
        }
        table {
            font-size: 16px;
            width: 66%;
            margin: 12.5% auto;
        }
        .alert {
            background-color: yellow;
            font-size: 16px;
            border-radius: 16px;
            padding: 16px;
            overflow-x: scroll;
        }
        body {
            padding: 10px;
        }
    </style>
</head>
<body>
    @if ($errors->any())
        <pre>
            <code class='alert alert-errors'>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </code>
        </pre>
    @endif

    <table cellspacing='0' cellpadding='10px' width=100%>
        <form action="/upload" method="POST" enctype="multipart/form-data">
            @csrf
            <tr>
                <td width='33%'>
                    <label for="excel">Upload Excel Spreadsheet:</label>
                </td>
                <td colspan="2">
                    <input type="file" name="excel" id="excel" required>
                </td>
            </tr>
            <tr>
                <td width='33%'>
                    <label for="template">Upload Word Template:</label>
                </td>
                <td colspan="2">
                    <input type="file" name="template" required>
                </td>
            </tr>
            <tr align='center'>
                <td colspan="3"><button style='width:100%;height:32px' type="submit">Upload</button></td>
            </tr>
        </form>
    </table>
</body>
</html>
