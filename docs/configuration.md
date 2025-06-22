# Configuration

This page provides a comprehensive reference for configuring the Elegant Maker Bundle.

## Basic Configuration

The bundle can be configured in your application's `config/packages/elegant_maker.yaml` file:

```yaml
elegant_maker:
    namespace_prefix: 'App'
    dir_prefix: 'src'
    
    # Generator-specific configurations
    exception:
        # ...
    controller:
        # ...
    collection:
        # ...
```

## Global Configuration Options

| Option | Description | Default Value |
|--------|-------------|---------------|
| `namespace_prefix` | The namespace prefix for all generated classes | `App` |
| `dir_prefix` | The directory prefix for all generated files | `src` |

## Generator-Specific Configuration

### Exception Generator Configuration

```yaml
elegant_maker:
    exception:
        root_namespace: 'App\Exception'
        target_directory: 'src/Exception'
```

| Option | Description | Default Value |
|--------|-------------|---------------|
| `root_namespace` | The namespace for generated exceptions | `App\Exception` |
| `target_directory` | The directory for generated exceptions | `src/Exception` |

### Controller Generator Configuration

```yaml
elegant_maker:
    controller:
        root_namespace: 'App\Controller'
        target_directory: 'src/Controller'
        template_path: 'controller.tpl.php'
        interfaces: []
```

| Option | Description | Default Value |
|--------|-------------|---------------|
| `root_namespace` | The namespace for generated controllers | `App\Controller` |
| `target_directory` | The directory for generated controllers | `src/Controller` |
| `template_path` | The template path for generated controllers | `controller.tpl.php` |
| `interfaces` | List of interfaces that the controller should implement | `[]` |

### Collection Generator Configuration

```yaml
elegant_maker:
    collection:
        root_namespace: 'App\Collection'
        target_directory: 'src/Collection'
        allowed_namespaces: ['App\DTO', 'App\Entity', 'App\ValueObject']
```

| Option | Description | Default Value |
|--------|-------------|---------------|
| `root_namespace` | The namespace for generated collections | `App\Collection` |
| `target_directory` | The directory for generated collections | `src/Collection` |
| `allowed_namespaces` | List of namespaces that can be used for collection elements | `['App\DTO', 'App\Entity', 'App\ValueObject']` |

## Complete Configuration Reference

Below is the complete configuration reference with all available options:

```yaml
elegant_maker:
    # Global configuration
    namespace_prefix: 'App'
    dir_prefix: 'src'
    
    # Exception generator configuration
    exception:
        root_namespace: 'App\Exception'
        target_directory: 'src/Exception'
    
    # Controller generator configuration
    controller:
        root_namespace: 'App\Controller'
        target_directory: 'src/Controller'
        template_path: 'controller.tpl.php'
        interfaces: []
    
    # Collection generator configuration
    collection:
        root_namespace: 'App\Collection'
        target_directory: 'src/Collection'
        allowed_namespaces: ['App\DTO', 'App\Entity', 'App\ValueObject']
```
