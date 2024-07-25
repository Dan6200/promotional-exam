<!DOCTYPE html>
<html>
<head>
    <title>Template Generator</title>
    <style>
        .alert-success {
            background-color: green;
        }
        .alert-errors {
            background-color: red;
        }
        h1 {
            text-align: center;
        }
        main {
            font-size: 16px;
            width: 75%;
            margin: 12.5% auto;
        }
        .alert button {
            color: red;
            height: 48px;
        }
        .alert span {
            display: inline;
        }
        .alert {
            justify-content: space-between;
            display: flex;
            color: white;
            font-size: 16px;
            font-weight: bold;
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
    @if (session('success'))
        <div class="alert alert-success">
            <span>{{ session('success') }}</span>
            <button onclick="closeAlert()">X</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-errors">
            <span>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </span>
            <button onclick="closeAlert()">X</button>
        </div>
    @endif
    <main>
        <h1>Upload Documents</h1>
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
    </main>
    <script>
        function closeAlert() {
            document.querySelector('.alert').style='display:none;'
        }
    </script>
</body>
</html>
