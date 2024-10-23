<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        /* General body and font styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        /* Success message styling */
        .success-message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #2ecc71;
            color: white;
            border-radius: 4px;
        }

        /* Form styling */
        form {
            max-width: 500px; /* Reduced width */
            margin: 0 auto;
            background-color: #fff;
            padding: 60px;
            padding-bottom: 4px;
            padding-top: 25px; /* Reduced padding */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px; /* Reduced margin */
            font-weight: bold;
            font-size: 14px; /* Smaller font size */
        }

        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px; /* Reduced padding */
            margin-bottom: 15px; /* Reduced margin */
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
        }

        textarea {
            height: 80px; /* Reduced height */
        }

        button {
            width: 100%;
            padding: 8px; /* Reduced padding */
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        /* Link to go back to products */
        .back-link {
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 8px; /* Reduced padding */
            background-color: #27ae60;
            color: white;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
        }

        .back-link:hover {
            background-color: #219150;
        }

        /* Table for buttons */
        .button-table {
            margin: 20px auto; /* Center the table */
            width: 500px; /* Fixed width for consistency */
            border-collapse: collapse; /* Remove spaces between cells */
        }

        .button-table td {
            padding: 10px; /* Space between buttons */
        }

    </style>
</head>
<body>
    <h1>Edit Product</h1>

    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" id="edit-product-form">
        @csrf
        @method('PUT') <!-- This is important for update method -->

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description">{{ old('description', $product->description) }}</textarea>
    </form>

    <table class="button-table">
        <tr>
            <td>
                <!-- Keeping the button outside the form, but ensuring the correct form ID is referenced -->
                <button type="submit" form="edit-product-form">Update Product</button>
            </td>
        </tr>
        <tr>
            <td>
                <a href="{{ route('products.index') }}" class="back-link">Back to Products</a>
            </td>
        </tr>
    </table>
</body>
</html>
