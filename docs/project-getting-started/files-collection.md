# Files collection

## Files
- Collection
  - SplFileInfoCollection.php

## SplFileInfoCollection

Create a collection of `\Symfony\Component\Finder\SplFileInfo` objects.

From array:

```php
use App\Files\Collection\SplFileInfoCollection;

$files = [
    new SplFileInfo('/path/to/file1'),
    new SplFileInfo('/path/to/file2'),
];

$collection = SplFileInfoCollection::createAsList($files);
```

From a `\Symfony\Component\Finder\Finder` object.

```php
use App\Files\Collection\SplFileInfoCollection;

$finder = new Finder();

$finder->files()->in('/path/to/files');

$collection = SplFileInfoCollection::fromFinder($finder);
```
