<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Transaction</title>
</head>
<body>
    <h1>Create New Sale</h1>

    @if(session('error'))
        <div>{{ session('error') }}</div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST">
        @csrf

        <label for="product">Product:</label>
        <select name="product_id" id="product" required>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} - ₱{{ $product->price }}</option>
            @endforeach
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" required>

        <button type="submit">Process Sale</button>
    </form>

    <a href="{{ route('products.index') }}">Back to Products</a>
</body>
</html>
