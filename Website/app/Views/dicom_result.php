<!DOCTYPE html>
<html>
<head>
    <title>DICOM Image</title>
</head>
<body>
    <h1>Processed DICOM Image</h1>
    <img src="data:image/png;base64,<?= esc($image_data) ?>" alt="DICOM Image">
</body>
</html>
