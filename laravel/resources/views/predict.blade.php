<!DOCTYPE html>
<html>
<head>
    <title>Sales Predictor</title>
</head>
<body>
    <form action="/predict" method="POST">
        @csrf
        <label for="sales">Enter last 12 months sales data (comma-separated):</label><br>
        <textarea id="sales" name="sales" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Predict Next Year Sales</button>
    </form>
</body>
</html>
