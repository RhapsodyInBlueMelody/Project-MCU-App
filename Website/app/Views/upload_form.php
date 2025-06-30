<!DOCTYPE html>  
<html>  
<head>  
    <title>Upload DICOM File</title>  
</head>  
<body>  
<!-- app/Views/upload_form.php -->
<form action="/dicom-processor/upload" method="post" enctype="multipart/form-data"> <!-- Fix enctype -->
    <input type="file" name="dicom_file" required>
    <button type="submit">Upload</button>
</form>
</body>  
</html>  
