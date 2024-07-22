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

    <table cellspacing='0' cellpadding='10px' width=100%>
        <form action="/upload" method="POST" enctype="multipart/form-data">
            @csrf
            <tr>
                <td width='25%'>
                    <label for="excel">Upload Excel Spreadsheet:</label>
                </td>
                <td colspan="2">
                    <input type="file" name="excel" id="excel" required>
                </td>
            </tr>
            <tr>
                <td width='25%'>
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
