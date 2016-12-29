## Extension

### debug

Static method enabling logging messages at a given level: `'error'`, `'warn'`, `'log'`, or `'info'`. Passing `true` is equivalent to passing `'log'`. Passing `false` disables all messages.

**Methods**

```javascript
Quill.debug(level: String | Boolean)
```

**Examples**

```javascript
Quill.debug('info');
```

### import

Static method returning Quill library, format, module, or theme. In general the path should map exactly to Quill source code directory structure. Unless stated otherwise, modification of returned entities may break required Quill functionality and is strongly discouraged.

**Methods**

```javascript
Quill.import(path): any
```

**Examples**

```javascript
var Parchment = Quill.import('parchment');
var Delta = Quill.import('delta');

var Toolbar = Quill.import('modules/toolbar');
var Link = Quill.import('formats/link');
// Similar to ES6 syntax `import Link from 'quill/formats/link';`
```

### register

Registers a module, theme, or format(s), making them available to be added to an editor. Can later be retrieved with [`Quill.import`](/docs/api/#import). Use the path prefix of `'formats/'`, `'modules/'`, or `'themes/'` for registering formats, modules or themes, respectively. Will overwrite existing definitions with the same path.

**Methods**

```javascript
Quill.register(path: String, def: any, supressWarning: Boolean = false)
Quill.register(defs: { [String]: any }, supressWarning: Boolean = false)
```

**Examples**

```javascript
var Module = Quill.import('core/module');

class CustomModule extends Module {}

Quill.register('modules/custom-module', Module);
```

```javascript
Quill.register({
  'formats/custom-format': CustomFormat,
  'modules/custom-module-a': CustomModuleA,
  'modules/custom-module-b': CustomModuleB,
});
```

### addContainer

Adds and returns a container element inside the Quill container, sibling to the editor itself. By convention, Quill modules should have a class name prefixed with `ql-`. Optionally include a refNode where container should be inserted before.

**Methods**

```javascript
addContainer(className: String, refNode?: Node): Element
addContainer(domNode: Node, refNode?: Node): Element
```

**Examples**

```javascript
var container = quill.addContainer('ql-custom');
```


### getModule

Retrieves a module that has been added to the editor.

**Methods**

```javascript
getModule(name: String): any
```

**Examples**

```javascript
var toolbar = quill.getModule('toolbar');
```


### disable

Shorthand for [`enable(false)`](#enable).


### enable

Set ability for user to edit, via input devices like the mouse or keyboard. Does not affect capabilities of API calls.

**Methods**

```javascript
enable(value: Boolean = true)
```

**Examples**

```javascript
quill.enable();
quill.enable(false);   // Disables user input
```

