<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>
<body>
    <h1>Edit Product</h1>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST">
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

        <button type="submit">Update Product</button>
    </form>

    <a href="{{ route('products.index') }}">Back to Products</a>
</body>
</html>
