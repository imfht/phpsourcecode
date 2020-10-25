//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

var Zepto = (function() {
  var undefined, key, $, classList, emptyArray = [], concat = emptyArray.concat, filter = emptyArray.filter, slice = emptyArray.slice,
    document = window.document,
    elementDisplay = {}, classCache = {},
    cssNumber = { 'column-count': 1, 'columns': 1, 'font-weight': 1, 'line-height': 1,'opacity': 1, 'z-index': 1, 'zoom': 1 },
    fragmentRE = /^\s*<(\w+|!)[^>]*>/,
    singleTagRE = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
    tagExpanderRE = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,
    rootNodeRE = /^(?:body|html)$/i,
    capitalRE = /([A-Z])/g,

    // special attributes that should be get/set via method calls
    methodAttributes = ['val', 'css', 'html', 'text', 'data', 'width', 'height', 'offset'],

    adjacencyOperators = [ 'after', 'prepend', 'before', 'append' ],
    table = document.createElement('table'),
    tableRow = document.createElement('tr'),
    containers = {
      'tr': document.createElement('tbody'),
      'tbody': table, 'thead': table, 'tfoot': table,
      'td': tableRow, 'th': tableRow,
      '*': document.createElement('div')
    },
    readyRE = /complete|loaded|interactive/,
    simpleSelectorRE = /^[\w-]*$/,
    class2type = {},
    toString = class2type.toString,
    zepto = {},
    camelize, uniq,
    tempParent = document.createElement('div'),
    propMap = {
      'tabindex': 'tabIndex',
      'readonly': 'readOnly',
      'for': 'htmlFor',
      'class': 'className',
      'maxlength': 'maxLength',
      'cellspacing': 'cellSpacing',
      'cellpadding': 'cellPadding',
      'rowspan': 'rowSpan',
      'colspan': 'colSpan',
      'usemap': 'useMap',
      'frameborder': 'frameBorder',
      'contenteditable': 'contentEditable'
    },
    isArray = Array.isArray ||
      function(object){ return object instanceof Array }

  zepto.matches = function(element, selector) {
    if (!selector || !element || element.nodeType !== 1) return false
    var matchesSelector = element.webkitMatchesSelector || element.mozMatchesSelector ||
                          element.oMatchesSelector || element.matchesSelector
    if (matchesSelector) return matchesSelector.call(element, selector)
    // fall back to performing a selector:
    var match, parent = element.parentNode, temp = !parent
    if (temp) (parent = tempParent).appendChild(element)
    match = ~zepto.qsa(parent, selector).indexOf(element)
    temp && tempParent.removeChild(element)
    return match
  }

  function type(obj) {
    return obj == null ? String(obj) :
      class2type[toString.call(obj)] || "object"
  }

  function isFunction(value) { return type(value) == "function" }
  function isWindow(obj)     { return obj != null && obj == obj.window }
  function isDocument(obj)   { return obj != null && obj.nodeType == obj.DOCUMENT_NODE }
  function isObject(obj)     { return type(obj) == "object" }
  function isPlainObject(obj) {
    return isObject(obj) && !isWindow(obj) && Object.getPrototypeOf(obj) == Object.prototype
  }
  function likeArray(obj) { return typeof obj.length == 'number' }

  function compact(array) { return filter.call(array, function(item){ return item != null }) }
  function flatten(array) { return array.length > 0 ? $.fn.concat.apply([], array) : array }
  camelize = function(str){ return str.replace(/-+(.)?/g, function(match, chr){ return chr ? chr.toUpperCase() : '' }) }
  function dasherize(str) {
    return str.replace(/::/g, '/')
           .replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
           .replace(/([a-z\d])([A-Z])/g, '$1_$2')
           .replace(/_/g, '-')
           .toLowerCase()
  }
  uniq = function(array){ return filter.call(array, function(item, idx){ return array.indexOf(item) == idx }) }

  function classRE(name) {
    return name in classCache ?
      classCache[name] : (classCache[name] = new RegExp('(^|\\s)' + name + '(\\s|$)'))
  }

  function maybeAddPx(name, value) {
    return (typeof value == "number" && !cssNumber[dasherize(name)]) ? value + "px" : value
  }

  function defaultDisplay(nodeName) {
    var element, display
    if (!elementDisplay[nodeName]) {
      element = document.createElement(nodeName)
      document.body.appendChild(element)
      display = getComputedStyle(element, '').getPropertyValue("display")
      element.parentNode.removeChild(element)
      display == "none" && (display = "block")
      elementDisplay[nodeName] = display
    }
    return elementDisplay[nodeName]
  }

  function children(element) {
    return 'children' in element ?
      slice.call(element.children) :
      $.map(element.childNodes, function(node){ if (node.nodeType == 1) return node })
  }

  function Z(dom, selector) {
    var i, len = dom ? dom.length : 0
    for (i = 0; i < len; i++) this[i] = dom[i]
    this.length = len
    this.selector = selector || ''
  }

  // `$.zepto.fragment` takes a html string and an optional tag name
  // to generate DOM nodes nodes from the given html string.
  // The generated DOM nodes are returned as an array.
  // This function can be overriden in plugins for example to make
  // it compatible with browsers that don't support the DOM fully.
  zepto.fragment = function(html, name, properties) {
    var dom, nodes, container

    // A special case optimization for a single tag
    if (singleTagRE.test(html)) dom = $(document.createElement(RegExp.$1))

    if (!dom) {
      if (html.replace) html = html.replace(tagExpanderRE, "<$1></$2>")
      if (name === undefined) name = fragmentRE.test(html) && RegExp.$1
      if (!(name in containers)) name = '*'

      container = containers[name]
      container.innerHTML = '' + html
      dom = $.each(slice.call(container.childNodes), function(){
        container.removeChild(this)
      })
    }

    if (isPlainObject(properties)) {
      nodes = $(dom)
      $.each(properties, function(key, value) {
        if (methodAttributes.indexOf(key) > -1) nodes[key](value)
        else nodes.attr(key, value)
      })
    }

    return dom
  }

  // `$.zepto.Z` swaps out the prototype of the given `dom` array
  // of nodes with `$.fn` and thus supplying all the Zepto functions
  // to the array. This method can be overriden in plugins.
  zepto.Z = function(dom, selector) {
    return new Z(dom, selector)
  }

  // `$.zepto.isZ` should return `true` if the given object is a Zepto
  // collection. This method can be overriden in plugins.
  zepto.isZ = function(object) {
    return object instanceof zepto.Z
  }

  // `$.zepto.init` is Zepto's counterpart to jQuery's `$.fn.init` and
  // takes a CSS selector and an optional context (and handles various
  // special cases).
  // This method can be overriden in plugins.
  zepto.init = function(selector, context) {
    var dom
    // If nothing given, return an empty Zepto collection
    if (!selector) return zepto.Z()
    // Optimize for string selectors
    else if (typeof selector == 'string') {
      selector = selector.trim()
      // If it's a html fragment, create nodes from it
      // Note: In both Chrome 21 and Firefox 15, DOM error 12
      // is thrown if the fragment doesn't begin with <
      if (selector[0] == '<' && fragmentRE.test(selector))
        dom = zepto.fragment(selector, RegExp.$1, context), selector = null
      // If there's a context, create a collection on that context first, and select
      // nodes from there
      else if (context !== undefined) return $(context).find(selector)
      // If it's a CSS selector, use it to select nodes.
      else dom = zepto.qsa(document, selector)
    }
    // If a function is given, call it when the DOM is ready
    else if (isFunction(selector)) return $(document).ready(selector)
    // If a Zepto collection is given, just return it
    else if (zepto.isZ(selector)) return selector
    else {
      // normalize array if an array of nodes is given
      if (isArray(selector)) dom = compact(selector)
      // Wrap DOM nodes.
      else if (isObject(selector))
        dom = [selector], selector = null
      // If it's a html fragment, create nodes from it
      else if (fragmentRE.test(selector))
        dom = zepto.fragment(selector.trim(), RegExp.$1, context), selector = null
      // If there's a context, create a collection on that context first, and select
      // nodes from there
      else if (context !== undefined) return $(context).find(selector)
      // And last but no least, if it's a CSS selector, use it to select nodes.
      else dom = zepto.qsa(document, selector)
    }
    // create a new Zepto collection from the nodes found
    return zepto.Z(dom, selector)
  }

  // `$` will be the base `Zepto` object. When calling this
  // function just call `$.zepto.init, which makes the implementation
  // details of selecting nodes and creating Zepto collections
  // patchable in plugins.
  $ = function(selector, context){
    return zepto.init(selector, context)
  }

  function extend(target, source, deep) {
    for (key in source)
      if (deep && (isPlainObject(source[key]) || isArray(source[key]))) {
        if (isPlainObject(source[key]) && !isPlainObject(target[key]))
          target[key] = {}
        if (isArray(source[key]) && !isArray(target[key]))
          target[key] = []
        extend(target[key], source[key], deep)
      }
      else if (source[key] !== undefined) target[key] = source[key]
  }

  // Copy all but undefined properties from one or more
  // objects to the `target` object.
  $.extend = function(target){
    var deep, args = slice.call(arguments, 1)
    if (typeof target == 'boolean') {
      deep = target
      target = args.shift()
    }
    args.forEach(function(arg){ extend(target, arg, deep) })
    return target
  }

  // `$.zepto.qsa` is Zepto's CSS selector implementation which
  // uses `document.querySelectorAll` and optimizes for some special cases, like `#id`.
  // This method can be overriden in plugins.
  zepto.qsa = function(element, selector){
    var found,
        maybeID = selector[0] == '#',
        maybeClass = !maybeID && selector[0] == '.',
        nameOnly = maybeID || maybeClass ? selector.slice(1) : selector, // Ensure that a 1 char tag name still gets checked
        isSimple = simpleSelectorRE.test(nameOnly)
    return (element.getElementById && isSimple && maybeID) ? // Safari DocumentFragment doesn't have getElementById
      ( (found = element.getElementById(nameOnly)) ? [found] : [] ) :
      (element.nodeType !== 1 && element.nodeType !== 9 && element.nodeType !== 11) ? [] :
      slice.call(
        isSimple && !maybeID && element.getElementsByClassName ? // DocumentFragment doesn't have getElementsByClassName/TagName
          maybeClass ? element.getElementsByClassName(nameOnly) : // If it's simple, it could be a class
          element.getElementsByTagName(selector) : // Or a tag
          element.querySelectorAll(selector) // Or it's not simple, and we need to query all
      )
  }

  function filtered(nodes, selector) {
    return selector == null ? $(nodes) : $(nodes).filter(selector)
  }

  $.contains = document.documentElement.contains ?
    function(parent, node) {
      return parent !== node && parent.contains(node)
    } :
    function(parent, node) {
      while (node && (node = node.parentNode))
        if (node === parent) return true
      return false
    }

  function funcArg(context, arg, idx, payload) {
    return isFunction(arg) ? arg.call(context, idx, payload) : arg
  }

  function setAttribute(node, name, value) {
    value == null ? node.removeAttribute(name) : node.setAttribute(name, value)
  }

  // access className property while respecting SVGAnimatedString
  function className(node, value){
    var klass = node.className || '',
        svg   = klass && klass.baseVal !== undefined

    if (value === undefined) return svg ? klass.baseVal : klass
    svg ? (klass.baseVal = value) : (node.className = value)
  }

  // "true"  => true
  // "false" => false
  // "null"  => null
  // "42"    => 42
  // "42.5"  => 42.5
  // "08"    => "08"
  // JSON    => parse if valid
  // String  => self
  function deserializeValue(value) {
    try {
      return value ?
        value == "true" ||
        ( value == "false" ? false :
          value == "null" ? null :
          +value + "" == value ? +value :
          /^[\[\{]/.test(value) ? $.parseJSON(value) :
          value )
        : value
    } catch(e) {
      return value
    }
  }

  $.type = type
  $.isFunction = isFunction
  $.isWindow = isWindow
  $.isArray = isArray
  $.isPlainObject = isPlainObject

  $.isEmptyObject = function(obj) {
    var name
    for (name in obj) return false
    return true
  }

  $.inArray = function(elem, array, i){
    return emptyArray.indexOf.call(array, elem, i)
  }

  $.camelCase = camelize
  $.trim = function(str) {
    return str == null ? "" : String.prototype.trim.call(str)
  }

  // plugin compatibility
  $.uuid = 0
  $.support = { }
  $.expr = { }
  $.noop = function() {}

  $.map = function(elements, callback){
    var value, values = [], i, key
    if (likeArray(elements))
      for (i = 0; i < elements.length; i++) {
        value = callback(elements[i], i)
        if (value != null) values.push(value)
      }
    else
      for (key in elements) {
        value = callback(elements[key], key)
        if (value != null) values.push(value)
      }
    return flatten(values)
  }

  $.each = function(elements, callback){
    var i, key
    if (likeArray(elements)) {
      for (i = 0; i < elements.length; i++)
        if (callback.call(elements[i], i, elements[i]) === false) return elements
    } else {
      for (key in elements)
        if (callback.call(elements[key], key, elements[key]) === false) return elements
    }

    return elements
  }

  $.grep = function(elements, callback){
    return filter.call(elements, callback)
  }

  if (window.JSON) $.parseJSON = JSON.parse

  // Populate the class2type map
  $.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function(i, name) {
    class2type[ "[object " + name + "]" ] = name.toLowerCase()
  })

  // Define methods that will be available on all
  // Zepto collections
  $.fn = {
    constructor: zepto.Z,
    length: 0,

    // Because a collection acts like an array
    // copy over these useful array functions.
    forEach: emptyArray.forEach,
    reduce: emptyArray.reduce,
    push: emptyArray.push,
    sort: emptyArray.sort,
    splice: emptyArray.splice,
    indexOf: emptyArray.indexOf,
    concat: function(){
      var i, value, args = []
      for (i = 0; i < arguments.length; i++) {
        value = arguments[i]
        args[i] = zepto.isZ(value) ? value.toArray() : value
      }
      return concat.apply(zepto.isZ(this) ? this.toArray() : this, args)
    },

    // `map` and `slice` in the jQuery API work differently
    // from their array counterparts
    map: function(fn){
      return $($.map(this, function(el, i){ return fn.call(el, i, el) }))
    },
    slice: function(){
      return $(slice.apply(this, arguments))
    },

    ready: function(callback){
      // need to check if document.body exists for IE as that browser reports
      // document ready when it hasn't yet created the body element
      if (readyRE.test(document.readyState) && document.body) callback($)
      else document.addEventListener('DOMContentLoaded', function(){ callback($) }, false)
      return this
    },
    get: function(idx){
      return idx === undefined ? slice.call(this) : this[idx >= 0 ? idx : idx + this.length]
    },
    toArray: function(){ return this.get() },
    size: function(){
      return this.length
    },
    remove: function(){
      return this.each(function(){
        if (this.parentNode != null)
          this.parentNode.removeChild(this)
      })
    },
    each: function(callback){
      emptyArray.every.call(this, function(el, idx){
        return callback.call(el, idx, el) !== false
      })
      return this
    },
    filter: function(selector){
      if (isFunction(selector)) return this.not(this.not(selector))
      return $(filter.call(this, function(element){
        return zepto.matches(element, selector)
      }))
    },
    add: function(selector,context){
      return $(uniq(this.concat($(selector,context))))
    },
    is: function(selector){
      return this.length > 0 && zepto.matches(this[0], selector)
    },
    not: function(selector){
      var nodes=[]
      if (isFunction(selector) && selector.call !== undefined)
        this.each(function(idx){
          if (!selector.call(this,idx)) nodes.push(this)
        })
      else {
        var excludes = typeof selector == 'string' ? this.filter(selector) :
          (likeArray(selector) && isFunction(selector.item)) ? slice.call(selector) : $(selector)
        this.forEach(function(el){
          if (excludes.indexOf(el) < 0) nodes.push(el)
        })
      }
      return $(nodes)
    },
    has: function(selector){
      return this.filter(function(){
        return isObject(selector) ?
          $.contains(this, selector) :
          $(this).find(selector).size()
      })
    },
    eq: function(idx){
      return idx === -1 ? this.slice(idx) : this.slice(idx, + idx + 1)
    },
    first: function(){
      var el = this[0]
      return el && !isObject(el) ? el : $(el)
    },
    last: function(){
      var el = this[this.length - 1]
      return el && !isObject(el) ? el : $(el)
    },
    find: function(selector){
      var result, $this = this
      if (!selector) result = $()
      else if (typeof selector == 'object')
        result = $(selector).filter(function(){
          var node = this
          return emptyArray.some.call($this, function(parent){
            return $.contains(parent, node)
          })
        })
      else if (this.length == 1) result = $(zepto.qsa(this[0], selector))
      else result = this.map(function(){ return zepto.qsa(this, selector) })
      return result
    },
    closest: function(selector, context){
      var node = this[0], collection = false
      if (typeof selector == 'object') collection = $(selector)
      while (node && !(collection ? collection.indexOf(node) >= 0 : zepto.matches(node, selector)))
        node = node !== context && !isDocument(node) && node.parentNode
      return $(node)
    },
    parents: function(selector){
      var ancestors = [], nodes = this
      while (nodes.length > 0)
        nodes = $.map(nodes, function(node){
          if ((node = node.parentNode) && !isDocument(node) && ancestors.indexOf(node) < 0) {
            ancestors.push(node)
            return node
          }
        })
      return filtered(ancestors, selector)
    },
    parent: function(selector){
      return filtered(uniq(this.pluck('parentNode')), selector)
    },
    children: function(selector){
      return filtered(this.map(function(){ return children(this) }), selector)
    },
    contents: function() {
      return this.map(function() { return this.contentDocument || slice.call(this.childNodes) })
    },
    siblings: function(selector){
      return filtered(this.map(function(i, el){
        return filter.call(children(el.parentNode), function(child){ return child!==el })
      }), selector)
    },
    empty: function(){
      return this.each(function(){ this.innerHTML = '' })
    },
    // `pluck` is borrowed from Prototype.js
    pluck: function(property){
      return $.map(this, function(el){ return el[property] })
    },
    show: function(){
      return this.each(function(){
        this.style.display == "none" && (this.style.display = '')
        if (getComputedStyle(this, '').getPropertyValue("display") == "none")
          this.style.display = defaultDisplay(this.nodeName)
      })
    },
    replaceWith: function(newContent){
      return this.before(newContent).remove()
    },
    wrap: function(structure){
      var func = isFunction(structure)
      if (this[0] && !func)
        var dom   = $(structure).get(0),
            clone = dom.parentNode || this.length > 1

      return this.each(function(index){
        $(this).wrapAll(
          func ? structure.call(this, index) :
            clone ? dom.cloneNode(true) : dom
        )
      })
    },
    wrapAll: function(structure){
      if (this[0]) {
        $(this[0]).before(structure = $(structure))
        var children
        // drill down to the inmost element
        while ((children = structure.children()).length) structure = children.first()
        $(structure).append(this)
      }
      return this
    },
    wrapInner: function(structure){
      var func = isFunction(structure)
      return this.each(function(index){
        var self = $(this), contents = self.contents(),
            dom  = func ? structure.call(this, index) : structure
        contents.length ? contents.wrapAll(dom) : self.append(dom)
      })
    },
    unwrap: function(){
      this.parent().each(function(){
        $(this).replaceWith($(this).children())
      })
      return this
    },
    clone: function(){
      return this.map(function(){ return this.cloneNode(true) })
    },
    hide: function(){
      return this.css("display", "none")
    },
    toggle: function(setting){
      return this.each(function(){
        var el = $(this)
        ;(setting === undefined ? el.css("display") == "none" : setting) ? el.show() : el.hide()
      })
    },
    prev: function(selector){ return $(this.pluck('previousElementSibling')).filter(selector || '*') },
    next: function(selector){ return $(this.pluck('nextElementSibling')).filter(selector || '*') },
    html: function(html){
      return 0 in arguments ?
        this.each(function(idx){
          var originHtml = this.innerHTML
          $(this).empty().append( funcArg(this, html, idx, originHtml) )
        }) :
        (0 in this ? this[0].innerHTML : null)
    },
    text: function(text){
      return 0 in arguments ?
        this.each(function(idx){
          var newText = funcArg(this, text, idx, this.textContent)
          this.textContent = newText == null ? '' : ''+newText
        }) :
        (0 in this ? this[0].textContent : null)
    },
    attr: function(name, value){
      var result
      return (typeof name == 'string' && !(1 in arguments)) ?
        (!this.length || this[0].nodeType !== 1 ? undefined :
          (!(result = this[0].getAttribute(name)) && name in this[0]) ? this[0][name] : result
        ) :
        this.each(function(idx){
          if (this.nodeType !== 1) return
          if (isObject(name)) for (key in name) setAttribute(this, key, name[key])
          else setAttribute(this, name, funcArg(this, value, idx, this.getAttribute(name)))
        })
    },
    removeAttr: function(name){
      return this.each(function(){ this.nodeType === 1 && name.split(' ').forEach(function(attribute){
        setAttribute(this, attribute)
      }, this)})
    },
    prop: function(name, value){
      name = propMap[name] || name
      return (1 in arguments) ?
        this.each(function(idx){
          this[name] = funcArg(this, value, idx, this[name])
        }) :
        (this[0] && this[0][name])
    },
    data: function(name, value){
      var attrName = 'data-' + name.replace(capitalRE, '-$1').toLowerCase()

      var data = (1 in arguments) ?
        this.attr(attrName, value) :
        this.attr(attrName)

      return data !== null ? deserializeValue(data) : undefined
    },
    val: function(value){
      return 0 in arguments ?
        this.each(function(idx){
          this.value = funcArg(this, value, idx, this.value)
        }) :
        (this[0] && (this[0].multiple ?
           $(this[0]).find('option').filter(function(){ return this.selected }).pluck('value') :
           this[0].value)
        )
    },
    offset: function(coordinates){
      if (coordinates) return this.each(function(index){
        var $this = $(this),
            coords = funcArg(this, coordinates, index, $this.offset()),
            parentOffset = $this.offsetParent().offset(),
            props = {
              top:  coords.top  - parentOffset.top,
              left: coords.left - parentOffset.left
            }

        if ($this.css('position') == 'static') props['position'] = 'relative'
        $this.css(props)
      })
      if (!this.length) return null
      if (!$.contains(document.documentElement, this[0]))
        return {top: 0, left: 0}
      var obj = this[0].getBoundingClientRect()
      return {
        left: obj.left + window.pageXOffset,
        top: obj.top + window.pageYOffset,
        width: Math.round(obj.width),
        height: Math.round(obj.height)
      }
    },
    css: function(property, value){
      if (arguments.length < 2) {
        var computedStyle, element = this[0]
        if(!element) return
        computedStyle = getComputedStyle(element, '')
        if (typeof property == 'string')
          return element.style[camelize(property)] || computedStyle.getPropertyValue(property)
        else if (isArray(property)) {
          var props = {}
          $.each(property, function(_, prop){
            props[prop] = (element.style[camelize(prop)] || computedStyle.getPropertyValue(prop))
          })
          return props
        }
      }

      var css = ''
      if (type(property) == 'string') {
        if (!value && value !== 0)
          this.each(function(){ this.style.removeProperty(dasherize(property)) })
        else
          css = dasherize(property) + ":" + maybeAddPx(property, value)
      } else {
        for (key in property)
          if (!property[key] && property[key] !== 0)
            this.each(function(){ this.style.removeProperty(dasherize(key)) })
          else
            css += dasherize(key) + ':' + maybeAddPx(key, property[key]) + ';'
      }

      return this.each(function(){ this.style.cssText += ';' + css })
    },
    index: function(element){
      return element ? this.indexOf($(element)[0]) : this.parent().children().indexOf(this[0])
    },
    hasClass: function(name){
      if (!name) return false
      return emptyArray.some.call(this, function(el){
        return this.test(className(el))
      }, classRE(name))
    },
    addClass: function(name){
      if (!name) return this
      return this.each(function(idx){
        if (!('className' in this)) return
        classList = []
        var cls = className(this), newName = funcArg(this, name, idx, cls)
        newName.split(/\s+/g).forEach(function(klass){
          if (!$(this).hasClass(klass)) classList.push(klass)
        }, this)
        classList.length && className(this, cls + (cls ? " " : "") + classList.join(" "))
      })
    },
    removeClass: function(name){
      return this.each(function(idx){
        if (!('className' in this)) return
        if (name === undefined) return className(this, '')
        classList = className(this)
        funcArg(this, name, idx, classList).split(/\s+/g).forEach(function(klass){
          classList = classList.replace(classRE(klass), " ")
        })
        className(this, classList.trim())
      })
    },
    toggleClass: function(name, when){
      if (!name) return this
      return this.each(function(idx){
        var $this = $(this), names = funcArg(this, name, idx, className(this))
        names.split(/\s+/g).forEach(function(klass){
          (when === undefined ? !$this.hasClass(klass) : when) ?
            $this.addClass(klass) : $this.removeClass(klass)
        })
      })
    },
    scrollTop: function(value){
      if (!this.length) return
      var hasScrollTop = 'scrollTop' in this[0]
      if (value === undefined) return hasScrollTop ? this[0].scrollTop : this[0].pageYOffset
      return this.each(hasScrollTop ?
        function(){ this.scrollTop = value } :
        function(){ this.scrollTo(this.scrollX, value) })
    },
    scrollLeft: function(value){
      if (!this.length) return
      var hasScrollLeft = 'scrollLeft' in this[0]
      if (value === undefined) return hasScrollLeft ? this[0].scrollLeft : this[0].pageXOffset
      return this.each(hasScrollLeft ?
        function(){ this.scrollLeft = value } :
        function(){ this.scrollTo(value, this.scrollY) })
    },
    position: function() {
      if (!this.length) return

      var elem = this[0],
        // Get *real* offsetParent
        offsetParent = this.offsetParent(),
        // Get correct offsets
        offset       = this.offset(),
        parentOffset = rootNodeRE.test(offsetParent[0].nodeName) ? { top: 0, left: 0 } : offsetParent.offset()

      // Subtract element margins
      // note: when an element has margin: auto the offsetLeft and marginLeft
      // are the same in Safari causing offset.left to incorrectly be 0
      offset.top  -= parseFloat( $(elem).css('margin-top') ) || 0
      offset.left -= parseFloat( $(elem).css('margin-left') ) || 0

      // Add offsetParent borders
      parentOffset.top  += parseFloat( $(offsetParent[0]).css('border-top-width') ) || 0
      parentOffset.left += parseFloat( $(offsetParent[0]).css('border-left-width') ) || 0

      // Subtract the two offsets
      return {
        top:  offset.top  - parentOffset.top,
        left: offset.left - parentOffset.left
      }
    },
    offsetParent: function() {
      return this.map(function(){
        var parent = this.offsetParent || document.body
        while (parent && !rootNodeRE.test(parent.nodeName) && $(parent).css("position") == "static")
          parent = parent.offsetParent
        return parent
      })
    }
  }

  // for now
  $.fn.detach = $.fn.remove

  // Generate the `width` and `height` functions
  ;['width', 'height'].forEach(function(dimension){
    var dimensionProperty =
      dimension.replace(/./, function(m){ return m[0].toUpperCase() })

    $.fn[dimension] = function(value){
      var offset, el = this[0]
      if (value === undefined) return isWindow(el) ? el['inner' + dimensionProperty] :
        isDocument(el) ? el.documentElement['scroll' + dimensionProperty] :
        (offset = this.offset()) && offset[dimension]
      else return this.each(function(idx){
        el = $(this)
        el.css(dimension, funcArg(this, value, idx, el[dimension]()))
      })
    }
  })

  function traverseNode(node, fun) {
    fun(node)
    for (var i = 0, len = node.childNodes.length; i < len; i++)
      traverseNode(node.childNodes[i], fun)
  }

  // Generate the `after`, `prepend`, `before`, `append`,
  // `insertAfter`, `insertBefore`, `appendTo`, and `prependTo` methods.
  adjacencyOperators.forEach(function(operator, operatorIndex) {
    var inside = operatorIndex % 2 //=> prepend, append

    $.fn[operator] = function(){
      // arguments can be nodes, arrays of nodes, Zepto objects and HTML strings
      var argType, nodes = $.map(arguments, function(arg) {
            argType = type(arg)
            return argType == "object" || argType == "array" || arg == null ?
              arg : zepto.fragment(arg)
          }),
          parent, copyByClone = this.length > 1
      if (nodes.length < 1) return this

      return this.each(function(_, target){
        parent = inside ? target : target.parentNode

        // convert all methods to a "before" operation
        target = operatorIndex == 0 ? target.nextSibling :
                 operatorIndex == 1 ? target.firstChild :
                 operatorIndex == 2 ? target :
                 null

        var parentInDocument = $.contains(document.documentElement, parent)

        nodes.forEach(function(node){
          if (copyByClone) node = node.cloneNode(true)
          else if (!parent) return $(node).remove()

          parent.insertBefore(node, target)
          if (parentInDocument) traverseNode(node, function(el){
            if (el.nodeName != null && el.nodeName.toUpperCase() === 'SCRIPT' &&
               (!el.type || el.type === 'text/javascript') && !el.src)
              window['eval'].call(window, el.innerHTML)
          })
        })
      })
    }

    // after    => insertAfter
    // prepend  => prependTo
    // before   => insertBefore
    // append   => appendTo
    $.fn[inside ? operator+'To' : 'insert'+(operatorIndex ? 'Before' : 'After')] = function(html){
      $(html)[operator](this)
      return this
    }
  })

  zepto.Z.prototype = Z.prototype = $.fn

  // Export internal API functions in the `$.zepto` namespace
  zepto.uniq = uniq
  zepto.deserializeValue = deserializeValue
  $.zepto = zepto

  return $
})()

// If `$` is not yet defined, point it to `Zepto`
window.Zepto = Zepto
window.$ === undefined && (window.$ = Zepto)

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($){
  var _zid = 1, undefined,
      slice = Array.prototype.slice,
      isFunction = $.isFunction,
      isString = function(obj){ return typeof obj == 'string' },
      handlers = {},
      specialEvents={},
      focusinSupported = 'onfocusin' in window,
      focus = { focus: 'focusin', blur: 'focusout' },
      hover = { mouseenter: 'mouseover', mouseleave: 'mouseout' }

  specialEvents.click = specialEvents.mousedown = specialEvents.mouseup = specialEvents.mousemove = 'MouseEvents'

  function zid(element) {
    return element._zid || (element._zid = _zid++)
  }
  function findHandlers(element, event, fn, selector) {
    event = parse(event)
    if (event.ns) var matcher = matcherFor(event.ns)
    return (handlers[zid(element)] || []).filter(function(handler) {
      return handler
        && (!event.e  || handler.e == event.e)
        && (!event.ns || matcher.test(handler.ns))
        && (!fn       || zid(handler.fn) === zid(fn))
        && (!selector || handler.sel == selector)
    })
  }
  function parse(event) {
    var parts = ('' + event).split('.')
    return {e: parts[0], ns: parts.slice(1).sort().join(' ')}
  }
  function matcherFor(ns) {
    return new RegExp('(?:^| )' + ns.replace(' ', ' .* ?') + '(?: |$)')
  }

  function eventCapture(handler, captureSetting) {
    return handler.del &&
      (!focusinSupported && (handler.e in focus)) ||
      !!captureSetting
  }

  function realEvent(type) {
    return hover[type] || (focusinSupported && focus[type]) || type
  }

  function add(element, events, fn, data, selector, delegator, capture){
    var id = zid(element), set = (handlers[id] || (handlers[id] = []))
    events.split(/\s/).forEach(function(event){
      if (event == 'ready') return $(document).ready(fn)
      var handler   = parse(event)
      handler.fn    = fn
      handler.sel   = selector
      // emulate mouseenter, mouseleave
      if (handler.e in hover) fn = function(e){
        var related = e.relatedTarget
        if (!related || (related !== this && !$.contains(this, related)))
          return handler.fn.apply(this, arguments)
      }
      handler.del   = delegator
      var callback  = delegator || fn
      handler.proxy = function(e){
        e = compatible(e)
        if (e.isImmediatePropagationStopped()) return
        e.data = data
        var result = callback.apply(element, e._args == undefined ? [e] : [e].concat(e._args))
        if (result === false) e.preventDefault(), e.stopPropagation()
        return result
      }
      handler.i = set.length
      set.push(handler)
      if ('addEventListener' in element)
        element.addEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture))
    })
  }
  function remove(element, events, fn, selector, capture){
    var id = zid(element)
    ;(events || '').split(/\s/).forEach(function(event){
      findHandlers(element, event, fn, selector).forEach(function(handler){
        delete handlers[id][handler.i]
      if ('removeEventListener' in element)
        element.removeEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture))
      })
    })
  }

  $.event = { add: add, remove: remove }

  $.proxy = function(fn, context) {
    var args = (2 in arguments) && slice.call(arguments, 2)
    if (isFunction(fn)) {
      var proxyFn = function(){ return fn.apply(context, args ? args.concat(slice.call(arguments)) : arguments) }
      proxyFn._zid = zid(fn)
      return proxyFn
    } else if (isString(context)) {
      if (args) {
        args.unshift(fn[context], fn)
        return $.proxy.apply(null, args)
      } else {
        return $.proxy(fn[context], fn)
      }
    } else {
      throw new TypeError("expected function")
    }
  }

  $.fn.bind = function(event, data, callback){
    return this.on(event, data, callback)
  }
  $.fn.unbind = function(event, callback){
    return this.off(event, callback)
  }
  $.fn.one = function(event, selector, data, callback){
    return this.on(event, selector, data, callback, 1)
  }

  var returnTrue = function(){return true},
      returnFalse = function(){return false},
      ignoreProperties = /^([A-Z]|returnValue$|layer[XY]$)/,
      eventMethods = {
        preventDefault: 'isDefaultPrevented',
        stopImmediatePropagation: 'isImmediatePropagationStopped',
        stopPropagation: 'isPropagationStopped'
      }

  function compatible(event, source) {
    if (source || !event.isDefaultPrevented) {
      source || (source = event)

      $.each(eventMethods, function(name, predicate) {
        var sourceMethod = source[name]
        event[name] = function(){
          this[predicate] = returnTrue
          return sourceMethod && sourceMethod.apply(source, arguments)
        }
        event[predicate] = returnFalse
      })

      if (source.defaultPrevented !== undefined ? source.defaultPrevented :
          'returnValue' in source ? source.returnValue === false :
          source.getPreventDefault && source.getPreventDefault())
        event.isDefaultPrevented = returnTrue
    }
    return event
  }

  function createProxy(event) {
    var key, proxy = { originalEvent: event }
    for (key in event)
      if (!ignoreProperties.test(key) && event[key] !== undefined) proxy[key] = event[key]

    return compatible(proxy, event)
  }

  $.fn.delegate = function(selector, event, callback){
    return this.on(event, selector, callback)
  }
  $.fn.undelegate = function(selector, event, callback){
    return this.off(event, selector, callback)
  }

  $.fn.live = function(event, callback){
    $(document.body).delegate(this.selector, event, callback)
    return this
  }
  $.fn.die = function(event, callback){
    $(document.body).undelegate(this.selector, event, callback)
    return this
  }

  $.fn.on = function(event, selector, data, callback, one){
    var autoRemove, delegator, $this = this
    if (event && !isString(event)) {
      $.each(event, function(type, fn){
        $this.on(type, selector, data, fn, one)
      })
      return $this
    }

    if (!isString(selector) && !isFunction(callback) && callback !== false)
      callback = data, data = selector, selector = undefined
    if (callback === undefined || data === false)
      callback = data, data = undefined

    if (callback === false) callback = returnFalse

    return $this.each(function(_, element){
      if (one) autoRemove = function(e){
        remove(element, e.type, callback)
        return callback.apply(this, arguments)
      }

      if (selector) delegator = function(e){
        var evt, match = $(e.target).closest(selector, element).get(0)
        if (match && match !== element) {
          evt = $.extend(createProxy(e), {currentTarget: match, liveFired: element})
          return (autoRemove || callback).apply(match, [evt].concat(slice.call(arguments, 1)))
        }
      }

      add(element, event, callback, data, selector, delegator || autoRemove)
    })
  }
  $.fn.off = function(event, selector, callback){
    var $this = this
    if (event && !isString(event)) {
      $.each(event, function(type, fn){
        $this.off(type, selector, fn)
      })
      return $this
    }

    if (!isString(selector) && !isFunction(callback) && callback !== false)
      callback = selector, selector = undefined

    if (callback === false) callback = returnFalse

    return $this.each(function(){
      remove(this, event, callback, selector)
    })
  }

  $.fn.trigger = function(event, args){
    event = (isString(event) || $.isPlainObject(event)) ? $.Event(event) : compatible(event)
    event._args = args
    return this.each(function(){
      // handle focus(), blur() by calling them directly
      if (event.type in focus && typeof this[event.type] == "function") this[event.type]()
      // items in the collection might not be DOM elements
      else if ('dispatchEvent' in this) this.dispatchEvent(event)
      else $(this).triggerHandler(event, args)
    })
  }

  // triggers event handlers on current element just as if an event occurred,
  // doesn't trigger an actual event, doesn't bubble
  $.fn.triggerHandler = function(event, args){
    var e, result
    this.each(function(i, element){
      e = createProxy(isString(event) ? $.Event(event) : event)
      e._args = args
      e.target = element
      $.each(findHandlers(element, event.type || event), function(i, handler){
        result = handler.proxy(e)
        if (e.isImmediatePropagationStopped()) return false
      })
    })
    return result
  }

  // shortcut methods for `.bind(event, fn)` for each event type
  ;('focusin focusout focus blur load resize scroll unload click dblclick '+
  'mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave '+
  'change select keydown keypress keyup error').split(' ').forEach(function(event) {
    $.fn[event] = function(callback) {
      return (0 in arguments) ?
        this.bind(event, callback) :
        this.trigger(event)
    }
  })

  $.Event = function(type, props) {
    if (!isString(type)) props = type, type = props.type
    var event = document.createEvent(specialEvents[type] || 'Events'), bubbles = true
    if (props) for (var name in props) (name == 'bubbles') ? (bubbles = !!props[name]) : (event[name] = props[name])
    event.initEvent(type, bubbles, true)
    return compatible(event)
  }

})(Zepto)

/*! touchjs v0.2.14  2014-08-05 */
'use strict';
(function(root, factory) {
    if (typeof define === 'function' && (define.amd || define.cmd)) {
        define(factory); //Register as a module.
    } else {
        root.touch = factory();
    }
}(this, function() {

var utils = {};

utils.PCevts = {
    'touchstart': 'mousedown',
    'touchmove': 'mousemove',
    'touchend': 'mouseup',
    'touchcancel': 'mouseout'
};

utils.hasTouch = ('ontouchstart' in window);

utils.getType = function(obj) {
    return Object.prototype.toString.call(obj).match(/\s([a-z|A-Z]+)/)[1].toLowerCase();
};

utils.getSelector = function(el) {
    if (el.id) {
        return "#" + el.id;
    }
    if (el.className) {
        var cns = el.className.split(/\s+/);
        return "." + cns.join(".");
    } else if (el === document) {
        return "body";
    } else {
        return el.tagName.toLowerCase();
    }
};

utils.matchSelector = function(target, selector) {
    return target.webkitMatchesSelector(selector);
};

utils.getEventListeners = function(el) {
    return el.listeners;
};

utils.getPCevts = function(evt) {
    return this.PCevts[evt] || evt;
};

utils.forceReflow = function() {
    var tempDivID = "reflowDivBlock";
    var domTreeOpDiv = document.getElementById(tempDivID);
    if (!domTreeOpDiv) {
        domTreeOpDiv = document.createElement("div");
        domTreeOpDiv.id = tempDivID;
        document.body.appendChild(domTreeOpDiv);
    }
    var parentNode = domTreeOpDiv.parentNode;
    var nextSibling = domTreeOpDiv.nextSibling;
    parentNode.removeChild(domTreeOpDiv);
    parentNode.insertBefore(domTreeOpDiv, nextSibling);
};

utils.simpleClone = function(obj) {
	return Object.create(obj);
};

utils.getPosOfEvent = function(ev) {
    if (this.hasTouch) {
        var posi = [];
        var src = null;

        for (var t = 0, len = ev.touches.length; t < len; t++) {
            src = ev.touches[t];
            posi.push({
                x: src.pageX,
                y: src.pageY
            });
        }
        return posi;
    } else {
        return [{
            x: ev.pageX,
            y: ev.pageY
        }];
    }
};

utils.getDistance = function(pos1, pos2) {
    var x = pos2.x - pos1.x,
        y = pos2.y - pos1.y;
    return Math.sqrt((x * x) + (y * y));
};

utils.getFingers = function(ev) {
    return ev.touches ? ev.touches.length : 1;
};

utils.calScale = function(pstart, pmove) {
    if (pstart.length >= 2 && pmove.length >= 2) {
        var disStart = this.getDistance(pstart[1], pstart[0]);
        var disEnd = this.getDistance(pmove[1], pmove[0]);

        return disEnd / disStart;
    }
    return 1;
};

utils.getAngle = function(p1, p2) {
    return Math.atan2(p2.y - p1.y, p2.x - p1.x) * 180 / Math.PI;
};

utils.getAngle180 = function(p1, p2) {
    var agl = Math.atan((p2.y - p1.y) * -1 / (p2.x - p1.x)) * (180 / Math.PI);
    return (agl < 0 ? (agl + 180) : agl);
};

utils.getDirectionFromAngle = function(agl) {
    var directions = {
        up: agl < -45 && agl > -135,
        down: agl >= 45 && agl < 135,
        left: agl >= 135 || agl <= -135,
        right: agl >= -45 && agl <= 45
    };
    for (var key in directions) {
        if (directions[key]) return key;
    }
    return null;
};

utils.getXYByElement = function(el) {
    var left = 0,
        top = 0;

    while (el.offsetParent) {
        left += el.offsetLeft;
        top += el.offsetTop;
        el = el.offsetParent;
    }
    return {
        left: left,
        top: top
    };
};

utils.reset = function() {
    startEvent = moveEvent = endEvent = null;
    __tapped = __touchStart = startSwiping = startPinch = false;
    startDrag = false;
    pos = {};
    __rotation_single_finger = false;
};

utils.isTouchMove = function(ev) {
    return (ev.type === 'touchmove' || ev.type === 'mousemove');
};

utils.isTouchEnd = function(ev) {
    return (ev.type === 'touchend' || ev.type === 'mouseup' || ev.type === 'touchcancel');
};

utils.env = (function() {
    var os = {}, ua = navigator.userAgent,
        android = ua.match(/(Android)[\s\/]+([\d\.]+)/),
        ios = ua.match(/(iPad|iPhone|iPod)\s+OS\s([\d_\.]+)/),
        wp = ua.match(/(Windows\s+Phone)\s([\d\.]+)/),
        isWebkit = /WebKit\/[\d.]+/i.test(ua),
        isSafari = ios ? (navigator.standalone ? isWebkit : (/Safari/i.test(ua) && !/CriOS/i.test(ua) && !/MQQBrowser/i.test(ua))) : false;
    if (android) {
        os.android = true;
        os.version = android[2];
    }
    if (ios) {
        os.ios = true;
        os.version = ios[2].replace(/_/g, '.');
        os.ios7 = /^7/.test(os.version);
        if (ios[1] === 'iPad') {
            os.ipad = true;
        } else if (ios[1] === 'iPhone') {
            os.iphone = true;
            os.iphone5 = screen.height == 568;
        } else if (ios[1] === 'iPod') {
            os.ipod = true;
        }
    }
    if (wp) {
        os.wp = true;
        os.version = wp[2];
        os.wp8 = /^8/.test(os.version);
    }
    if (isWebkit) {
        os.webkit = true;
    }
    if (isSafari) {
        os.safari = true;
    }
    return os;
})();

/** 底层事件绑定/代理支持  */
var engine = {
    proxyid: 0,
    proxies: [],
    trigger: function(el, evt, detail) {

        detail = detail || {};
        var e, opt = {
                bubbles: true,
                cancelable: true,
                detail: detail
            };

        try {
            if (typeof CustomEvent !== 'undefined') {
                e = new CustomEvent(evt, opt);
                if (el) {
                    el.dispatchEvent(e);
                }
            } else {
                e = document.createEvent("CustomEvent");
                e.initCustomEvent(evt, true, true, detail);
                if (el) {
                    el.dispatchEvent(e);
                }
            }
        } catch (ex) {
            console.warn("Touch.js is not supported by environment.");
        }
    },
    bind: function(el, evt, handler) {
        el.listeners = el.listeners || {};
        if (!el.listeners[evt]) {
            el.listeners[evt] = [handler];
        } else {
            el.listeners[evt].push(handler);
        }
        var proxy = function(e) {
            if (utils.env.ios7) {
                utils.forceReflow();
            }
            e.originEvent = e;
            for (var p in e.detail) {
                if (p !== 'type') {
                    e[p] = e.detail[p];
                }
            }
            e.startRotate = function() {
                __rotation_single_finger = true;
            };
            var returnValue = handler.call(e.target, e);
            if (typeof returnValue !== "undefined" && !returnValue) {
                e.stopPropagation();
                e.preventDefault();
            }
        };
        handler.proxy = handler.proxy || {};
        if (!handler.proxy[evt]) {
            handler.proxy[evt] = [this.proxyid++];
        } else {
            handler.proxy[evt].push(this.proxyid++);
        }
        this.proxies.push(proxy);
        if (el.addEventListener) {
            el.addEventListener(evt, proxy, false);
        }
    },
    unbind: function(el, evt, handler) {
        if (!handler) {
            var handlers = el.listeners[evt];
            if (handlers && handlers.length) {
                handlers.forEach(function(handler) {
                    el.removeEventListener(evt, handler, false);
                });
            }
        } else {
            var proxyids = handler.proxy[evt];
            if (proxyids && proxyids.length) {
                proxyids.forEach(function(proxyid) {
                    if (el.removeEventListener) {
                        el.removeEventListener(evt, this.proxies[this.proxyid], false);
                    }
                });
            }
        }
    },
    delegate: function(el, evt, sel, handler) {
        var proxy = function(e) {
            var target, returnValue;
            e.originEvent = e;
            for (var p in e.detail) {
                if (p !== 'type') {
                    e[p] = e.detail[p];
                }
            }
            e.startRotate = function() {
                __rotation_single_finger = true;
            };
            var integrateSelector = utils.getSelector(el) + " " + sel;
            var match = utils.matchSelector(e.target, integrateSelector);
            var ischild = utils.matchSelector(e.target, integrateSelector + " " + e.target.nodeName);
            if (!match && ischild) {
                if (utils.env.ios7) {
                    utils.forceReflow();
                }
                target = e.target;
                while (!utils.matchSelector(target, integrateSelector)) {
                    target = target.parentNode;
                }
                returnValue = handler.call(e.target, e);
                if (typeof returnValue !== "undefined" && !returnValue) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            } else {
                if (utils.env.ios7) {
                    utils.forceReflow();
                }
                if (match || ischild) {
                    returnValue = handler.call(e.target, e);
                    if (typeof returnValue !== "undefined" && !returnValue) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                }
            }
        };
        handler.proxy = handler.proxy || {};
        if (!handler.proxy[evt]) {
            handler.proxy[evt] = [this.proxyid++];
        } else {
            handler.proxy[evt].push(this.proxyid++);
        }
        this.proxies.push(proxy);
        el.listeners = el.listeners || {};
        if (!el.listeners[evt]) {
            el.listeners[evt] = [proxy];
        } else {
            el.listeners[evt].push(proxy);
        }
        if (el.addEventListener) {
            el.addEventListener(evt, proxy, false);
        }
    },
    undelegate: function(el, evt, sel, handler) {
        if (!handler) {
            var listeners = el.listeners[evt];
            listeners.forEach(function(proxy) {
                el.removeEventListener(evt, proxy, false);
            });
        } else {
            var proxyids = handler.proxy[evt];
            if (proxyids.length) {
                proxyids.forEach(function(proxyid) {
                    if (el.removeEventListener) {
                        el.removeEventListener(evt, this.proxies[this.proxyid], false);
                    }
                });
            }
        }
    }
};

var config = {
    tap: true,
    doubleTap: true,
    tapMaxDistance: 10,
    hold: true,
    tapTime: 200,
    holdTime: 650,
    maxDoubleTapInterval: 300,
    swipe: true,
    swipeTime: 300,
    swipeMinDistance: 18,
    swipeFactor: 5,
    drag: true,
    pinch: true,
    minScaleRate: 0,
    minRotationAngle: 0
};

var smrEventList = {
    TOUCH_START: 'touchstart',
    TOUCH_MOVE: 'touchmove',
    TOUCH_END: 'touchend',
    TOUCH_CANCEL: 'touchcancel',
    MOUSE_DOWN: 'mousedown',
    MOUSE_MOVE: 'mousemove',
    MOUSE_UP: 'mouseup',
    CLICK: 'click',
    PINCH_START: 'pinchstart',
    PINCH_END: 'pinchend',
    PINCH: 'pinch',
    PINCH_IN: 'pinchin',
    PINCH_OUT: 'pinchout',
    ROTATION_LEFT: 'rotateleft',
    ROTATION_RIGHT: 'rotateright',
    ROTATION: 'rotate',
    SWIPE_START: 'swipestart',
    SWIPING: 'swiping',
    SWIPE_END: 'swipeend',
    SWIPE_LEFT: 'swipeleft',
    SWIPE_RIGHT: 'swiperight',
    SWIPE_UP: 'swipeup',
    SWIPE_DOWN: 'swipedown',
    SWIPE: 'swipe',
    DRAG: 'drag',
    DRAGSTART: 'dragstart',
    DRAGEND: 'dragend',
    HOLD: 'hold',
    TAP: 'tap',
    DOUBLE_TAP: 'doubletap'
};

/** 手势识别 */
var pos = {
    start: null,
    move: null,
    end: null
};

var startTime = 0;
var fingers = 0;
var startEvent = null;
var moveEvent = null;
var endEvent = null;
var startSwiping = false;
var startPinch = false;
var startDrag = false;

var __offset = {};
var __touchStart = false;
var __holdTimer = null;
var __tapped = false;
var __lastTapEndTime = null;
var __tapTimer = null;

var __scale_last_rate = 1;
var __rotation_single_finger = false;
var __rotation_single_start = [];
var __initial_angle = 0;
var __rotation = 0;

var __prev_tapped_end_time = 0;
var __prev_tapped_pos = null;

var gestures = {
    getAngleDiff: function(currentPos) {
        var diff = parseInt(__initial_angle - utils.getAngle180(currentPos[0], currentPos[1]), 10);
        var count = 0;

        while (Math.abs(diff - __rotation) > 90 && count++ < 50) {
            if (__rotation < 0) {
                diff -= 180;
            } else {
                diff += 180;
            }
        }
        __rotation = parseInt(diff, 10);
        return __rotation;
    },
    pinch: function(ev) {
        var el = ev.target;
        if (config.pinch) {
            if (!__touchStart) return;
            if (utils.getFingers(ev) < 2) {
                if (!utils.isTouchEnd(ev)) return;
            }
            var scale = utils.calScale(pos.start, pos.move);
            var rotation = this.getAngleDiff(pos.move);
            var eventObj = {
                type: '',
                originEvent: ev,
                scale: scale,
                rotation: rotation,
                direction: (rotation > 0 ? 'right' : 'left'),
                fingersCount: utils.getFingers(ev)
            };
            if (!startPinch) {
                startPinch = true;
                eventObj.fingerStatus = "start";
                engine.trigger(el, smrEventList.PINCH_START, eventObj);
            } else if (utils.isTouchMove(ev)) {
                eventObj.fingerStatus = "move";
                engine.trigger(el, smrEventList.PINCH, eventObj);
            } else if (utils.isTouchEnd(ev)) {
                eventObj.fingerStatus = "end";
                engine.trigger(el, smrEventList.PINCH_END, eventObj);
                utils.reset();
            }

            if (Math.abs(1 - scale) > config.minScaleRate) {
                var scaleEv = utils.simpleClone(eventObj);

                //手势放大, 触发pinchout事件
                var scale_diff = 0.00000000001; //防止touchend的scale与__scale_last_rate相等，不触发事件的情况。
                if (scale > __scale_last_rate) {
                    __scale_last_rate = scale - scale_diff;
                    engine.trigger(el, smrEventList.PINCH_OUT, scaleEv, false);
                } //手势缩小,触发pinchin事件
                else if (scale < __scale_last_rate) {
                    __scale_last_rate = scale + scale_diff;
                    engine.trigger(el, smrEventList.PINCH_IN, scaleEv, false);
                }

                if (utils.isTouchEnd(ev)) {
                    __scale_last_rate = 1;
                }
            }

            if (Math.abs(rotation) > config.minRotationAngle) {
                var rotationEv = utils.simpleClone(eventObj),
                    eventType;

                eventType = rotation > 0 ? smrEventList.ROTATION_RIGHT : smrEventList.ROTATION_LEFT;
                engine.trigger(el, eventType, rotationEv, false);
                engine.trigger(el, smrEventList.ROTATION, eventObj);
            }

        }
    },
    rotateSingleFinger: function(ev) {
        var el = ev.target;
        if (__rotation_single_finger && utils.getFingers(ev) < 2) {
            if (!pos.move) return;
            if (__rotation_single_start.length < 2) {
                var docOff = utils.getXYByElement(el);

                __rotation_single_start = [{
                        x: docOff.left + el.offsetWidth / 2,
                        y: docOff.top + el.offsetHeight / 2
                    },
                    pos.move[0]
                ];
                __initial_angle = parseInt(utils.getAngle180(__rotation_single_start[0], __rotation_single_start[1]), 10);
            }
            var move = [__rotation_single_start[0], pos.move[0]];
            var rotation = this.getAngleDiff(move);
            var eventObj = {
                type: '',
                originEvent: ev,
                rotation: rotation,
                direction: (rotation > 0 ? 'right' : 'left'),
                fingersCount: utils.getFingers(ev)
            };
            if (utils.isTouchMove(ev)) {
                eventObj.fingerStatus = "move";
            } else if (utils.isTouchEnd(ev) || ev.type === 'mouseout') {
                eventObj.fingerStatus = "end";
                engine.trigger(el, smrEventList.PINCH_END, eventObj);
                utils.reset();
            }
            var eventType = rotation > 0 ? smrEventList.ROTATION_RIGHT : smrEventList.ROTATION_LEFT;
            engine.trigger(el, eventType, eventObj);
            engine.trigger(el, smrEventList.ROTATION, eventObj);
        }
    },
    swipe: function(ev) {
        var el = ev.target;
        if (!__touchStart || !pos.move || utils.getFingers(ev) > 1) {
            return;
        }

        var now = Date.now();
        var touchTime = now - startTime;
        var distance = utils.getDistance(pos.start[0], pos.move[0]);
        var position = {
            x: pos.move[0].x - __offset.left,
            y: pos.move[0].y - __offset.top
        };
        var angle = utils.getAngle(pos.start[0], pos.move[0]);
        var direction = utils.getDirectionFromAngle(angle);
        var touchSecond = touchTime / 1000;
        var factor = ((10 - config.swipeFactor) * 10 * touchSecond * touchSecond);
        var eventObj = {
            type: smrEventList.SWIPE,
            originEvent: ev,
            position: position,
            direction: direction,
            distance: distance,
            distanceX: pos.move[0].x - pos.start[0].x,
            distanceY: pos.move[0].y - pos.start[0].y,
            x: pos.move[0].x - pos.start[0].x,
            y: pos.move[0].y - pos.start[0].y,
            angle: angle,
            duration: touchTime,
            fingersCount: utils.getFingers(ev),
            factor: factor
        };
        if (config.swipe) {
            var swipeTo = function() {
                var elt = smrEventList;
                switch (direction) {
                    case 'up':
                        engine.trigger(el, elt.SWIPE_UP, eventObj);
                        break;
                    case 'down':
                        engine.trigger(el, elt.SWIPE_DOWN, eventObj);
                        break;
                    case 'left':
                        engine.trigger(el, elt.SWIPE_LEFT, eventObj);
                        break;
                    case 'right':
                        engine.trigger(el, elt.SWIPE_RIGHT, eventObj);
                        break;
                }
            };

            if (!startSwiping) {
                eventObj.fingerStatus = eventObj.swipe = 'start';
                startSwiping = true;
                engine.trigger(el, smrEventList.SWIPE_START, eventObj);
            } else if (utils.isTouchMove(ev)) {
                eventObj.fingerStatus = eventObj.swipe = 'move';
                engine.trigger(el, smrEventList.SWIPING, eventObj);

                if (touchTime > config.swipeTime && touchTime < config.swipeTime + 50 && distance > config.swipeMinDistance) {
                    swipeTo();
                    engine.trigger(el, smrEventList.SWIPE, eventObj, false);
                }
            } else if (utils.isTouchEnd(ev) || ev.type === 'mouseout') {
                eventObj.fingerStatus = eventObj.swipe = 'end';
                engine.trigger(el, smrEventList.SWIPE_END, eventObj);

                if (config.swipeTime > touchTime && distance > config.swipeMinDistance) {
                    swipeTo();
                    engine.trigger(el, smrEventList.SWIPE, eventObj, false);
                }
            }
        }

        if (config.drag) {
            if (!startDrag) {
                eventObj.fingerStatus = eventObj.swipe = 'start';
                startDrag = true;
                engine.trigger(el, smrEventList.DRAGSTART, eventObj);
            } else if (utils.isTouchMove(ev)) {
                eventObj.fingerStatus = eventObj.swipe = 'move';
                engine.trigger(el, smrEventList.DRAG, eventObj);
            } else if (utils.isTouchEnd(ev)) {
                eventObj.fingerStatus = eventObj.swipe = 'end';
                engine.trigger(el, smrEventList.DRAGEND, eventObj);
            }

        }
    },
    tap: function(ev) {
        var el = ev.target;
        if (config.tap) {
            var now = Date.now();
            var touchTime = now - startTime;
            var distance = utils.getDistance(pos.start[0], pos.move ? pos.move[0] : pos.start[0]);

            clearTimeout(__holdTimer);
            var isDoubleTap = (function() {
                if (__prev_tapped_pos && config.doubleTap && (startTime - __prev_tapped_end_time) < config.maxDoubleTapInterval) {
                    var doubleDis = utils.getDistance(__prev_tapped_pos, pos.start[0]);
                    if (doubleDis < 16) return true;
                }
                return false;
            })();

            if (isDoubleTap) {
                clearTimeout(__tapTimer);
                engine.trigger(el, smrEventList.DOUBLE_TAP, {
                    type: smrEventList.DOUBLE_TAP,
                    originEvent: ev,
                    position: pos.start[0]
                });
                return;
            }

            if (config.tapMaxDistance < distance) return;

            if (config.holdTime > touchTime && utils.getFingers(ev) <= 1) {
                __tapped = true;
                __prev_tapped_end_time = now;
                __prev_tapped_pos = pos.start[0];
                __tapTimer = setTimeout(function() {
                        engine.trigger(el, smrEventList.TAP, {
                            type: smrEventList.TAP,
                            originEvent: ev,
                            fingersCount: utils.getFingers(ev),
                            position: __prev_tapped_pos
                        });
                    },
                    config.tapTime);
            }
        }
    },
    hold: function(ev) {
        var el = ev.target;
        if (config.hold) {
            clearTimeout(__holdTimer);

            __holdTimer = setTimeout(function() {
                    if (!pos.start) return;
                    var distance = utils.getDistance(pos.start[0], pos.move ? pos.move[0] : pos.start[0]);
                    if (config.tapMaxDistance < distance) return;

                    if (!__tapped) {
                        engine.trigger(el, "hold", {
                            type: 'hold',
                            originEvent: ev,
                            fingersCount: utils.getFingers(ev),
                            position: pos.start[0]
                        });
                    }
                },
                config.holdTime);
        }
    }
};

var handlerOriginEvent = function(ev) {

    var el = ev.target;
    switch (ev.type) {
        case 'touchstart':
        case 'mousedown':
            __rotation_single_start = [];
            __touchStart = true;
            if (!pos.start || pos.start.length < 2) {
                pos.start = utils.getPosOfEvent(ev);
            }
            if (utils.getFingers(ev) >= 2) {
                __initial_angle = parseInt(utils.getAngle180(pos.start[0], pos.start[1]), 10);
            }

            startTime = Date.now();
            startEvent = ev;
            __offset = {};

            var box = el.getBoundingClientRect();
            var docEl = document.documentElement;
            __offset = {
                top: box.top + (window.pageYOffset || docEl.scrollTop) - (docEl.clientTop || 0),
                left: box.left + (window.pageXOffset || docEl.scrollLeft) - (docEl.clientLeft || 0)
            };

            gestures.hold(ev);
            break;
        case 'touchmove':
        case 'mousemove':
            if (!__touchStart || !pos.start) return;
            pos.move = utils.getPosOfEvent(ev);
            if (utils.getFingers(ev) >= 2) {
                gestures.pinch(ev);
            } else if (__rotation_single_finger) {
                gestures.rotateSingleFinger(ev);
            } else {
                gestures.swipe(ev);
            }
            break;
        case 'touchend':
        case 'touchcancel':
        case 'mouseup':
        case 'mouseout':
            if (!__touchStart) return;
            endEvent = ev;

            if (startPinch) {
                gestures.pinch(ev);
            } else if (__rotation_single_finger) {
                gestures.rotateSingleFinger(ev);
            } else if (startSwiping) {
                gestures.swipe(ev);
            } else {
                gestures.tap(ev);
            }

            utils.reset();
            __initial_angle = 0;
            __rotation = 0;
            if (ev.touches && ev.touches.length === 1) {
                __touchStart = true;
                __rotation_single_finger = true;
            }
            break;
    }
};

var _on = function() {

    var evts, handler, evtMap, sel, args = arguments;
    if (args.length < 2 || args > 4) {
        return console.error("unexpected arguments!");
    }
    var els = utils.getType(args[0]) === 'string' ? document.querySelectorAll(args[0]) : args[0];
    els = els.length ? Array.prototype.slice.call(els) : [els];
    //事件绑定
    if (args.length === 3 && utils.getType(args[1]) === 'string') {
        evts = args[1].split(" ");
        handler = args[2];
        evts.forEach(function(evt) {
            if (!utils.hasTouch) {
                evt = utils.getPCevts(evt);
            }
            els.forEach(function(el) {
                engine.bind(el, evt, handler);
            });
        });
        return;
    }

    function evtMapDelegate(evt) {
        if (!utils.hasTouch) {
            evt = utils.getPCevts(evt);
        }
        els.forEach(function(el) {
            engine.delegate(el, evt, sel, evtMap[evt]);
        });
    }
    //mapEvent delegate
    if (args.length === 3 && utils.getType(args[1]) === 'object') {
        evtMap = args[1];
        sel = args[2];
        for (var evt1 in evtMap) {
            evtMapDelegate(evt1);
        }
        return;
    }

    function evtMapBind(evt) {
        if (!utils.hasTouch) {
            evt = utils.getPCevts(evt);
        }
        els.forEach(function(el) {
            engine.bind(el, evt, evtMap[evt]);
        });
    }

    //mapEvent bind
    if (args.length === 2 && utils.getType(args[1]) === 'object') {
        evtMap = args[1];
        for (var evt2 in evtMap) {
            evtMapBind(evt2);
        }
        return;
    }

    //兼容factor config
    if (args.length === 4 && utils.getType(args[2]) === "object") {
        evts = args[1].split(" ");
        handler = args[3];
        evts.forEach(function(evt) {
            if (!utils.hasTouch) {
                evt = utils.getPCevts(evt);
            }
            els.forEach(function(el) {
                engine.bind(el, evt, handler);
            });
        });
        return;
    }

    //事件代理
    if (args.length === 4) {
        var el = els[0];
        evts = args[1].split(" ");
        sel = args[2];
        handler = args[3];
        evts.forEach(function(evt) {
            if (!utils.hasTouch) {
                evt = utils.getPCevts(evt);
            }
            engine.delegate(el, evt, sel, handler);
        });
        return;
    }
};

var _off = function() {
    var evts, handler;
    var args = arguments;
    if (args.length < 1 || args.length > 4) {
        return console.error("unexpected arguments!");
    }
    var els = utils.getType(args[0]) === 'string' ? document.querySelectorAll(args[0]) : args[0];
    els = els.length ? Array.prototype.slice.call(els) : [els];

    if (args.length === 1 || args.length === 2) {
        els.forEach(function(el) {
            evts = args[1] ? args[1].split(" ") : Object.keys(el.listeners);
            if (evts.length) {
                evts.forEach(function(evt) {
                    if (!utils.hasTouch) {
                        evt = utils.getPCevts(evt);
                    }
                    engine.unbind(el, evt);
                    engine.undelegate(el, evt);
                });
            }
        });
        return;
    }

    if (args.length === 3 && utils.getType(args[2]) === 'function') {
        handler = args[2];
        els.forEach(function(el) {
            evts = args[1].split(" ");
            evts.forEach(function(evt) {
                if (!utils.hasTouch) {
                    evt = utils.getPCevts(evt);
                }
                engine.unbind(el, evt, handler);
            });
        });
        return;
    }

    if (args.length === 3 && utils.getType(args[2]) === 'string') {
        var sel = args[2];
        els.forEach(function(el) {
            evts = args[1].split(" ");
            evts.forEach(function(evt) {
                if (!utils.hasTouch) {
                    evt = utils.getPCevts(evt);
                }
                engine.undelegate(el, evt, sel);
            });
        });
        return;
    }

    if (args.length === 4) {
        handler = args[3];
        els.forEach(function(el) {
            evts = args[1].split(" ");
            evts.forEach(function(evt) {
                if (!utils.hasTouch) {
                    evt = utils.getPCevts(evt);
                }
                engine.undelegate(el, evt, sel, handler);
            });
        });
        return;
    }
};

var _dispatch = function(el, evt, detail) {
    var args = arguments;
    if (!utils.hasTouch) {
        evt = utils.getPCevts(evt);
    }
    var els = utils.getType(args[0]) === 'string' ? document.querySelectorAll(args[0]) : args[0];
    els = els.length ? Array.prototype.call(els) : [els];

    els.forEach(function(el) {
        engine.trigger(el, evt, detail);
    });
};

    //init gesture
    function init() {

        var mouseEvents = 'mouseup mousedown mousemove mouseout',
            touchEvents = 'touchstart touchmove touchend touchcancel';
        var bindingEvents = utils.hasTouch ? touchEvents : mouseEvents;

        bindingEvents.split(" ").forEach(function(evt) {
            document.addEventListener(evt, handlerOriginEvent, false);
        });
    }

    init();

    var exports = {};

    exports.on = exports.bind = exports.live = _on;
    exports.off = exports.unbind = exports.die = _off;
    exports.config = config;
    exports.trigger = _dispatch;

    return exports;
}));

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($){
  var jsonpID = 0,
      document = window.document,
      key,
      name,
      rscript = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
      scriptTypeRE = /^(?:text|application)\/javascript/i,
      xmlTypeRE = /^(?:text|application)\/xml/i,
      jsonType = 'application/json',
      htmlType = 'text/html',
      blankRE = /^\s*$/,
      originAnchor = document.createElement('a')

  originAnchor.href = window.location.href

  // trigger a custom event and return false if it was cancelled
  function triggerAndReturn(context, eventName, data) {
    var event = $.Event(eventName)
    $(context).trigger(event, data)
    return !event.isDefaultPrevented()
  }

  // trigger an Ajax "global" event
  function triggerGlobal(settings, context, eventName, data) {
    if (settings.global) return triggerAndReturn(context || document, eventName, data)
  }

  // Number of active Ajax requests
  $.active = 0

  function ajaxStart(settings) {
    if (settings.global && $.active++ === 0) triggerGlobal(settings, null, 'ajaxStart')
  }
  function ajaxStop(settings) {
    if (settings.global && !(--$.active)) triggerGlobal(settings, null, 'ajaxStop')
  }

  // triggers an extra global event "ajaxBeforeSend" that's like "ajaxSend" but cancelable
  function ajaxBeforeSend(xhr, settings) {
    var context = settings.context
    if (settings.beforeSend.call(context, xhr, settings) === false ||
        triggerGlobal(settings, context, 'ajaxBeforeSend', [xhr, settings]) === false)
      return false

    triggerGlobal(settings, context, 'ajaxSend', [xhr, settings])
  }
  function ajaxSuccess(data, xhr, settings, deferred) {
    var context = settings.context, status = 'success'
    settings.success.call(context, data, status, xhr)
    if (deferred) deferred.resolveWith(context, [data, status, xhr])
    triggerGlobal(settings, context, 'ajaxSuccess', [xhr, settings, data])
    ajaxComplete(status, xhr, settings)
  }
  // type: "timeout", "error", "abort", "parsererror"
  function ajaxError(error, type, xhr, settings, deferred) {
    var context = settings.context
    settings.error.call(context, xhr, type, error)
    if (deferred) deferred.rejectWith(context, [xhr, type, error])
    triggerGlobal(settings, context, 'ajaxError', [xhr, settings, error || type])
    ajaxComplete(type, xhr, settings)
  }
  // status: "success", "notmodified", "error", "timeout", "abort", "parsererror"
  function ajaxComplete(status, xhr, settings) {
    var context = settings.context
    settings.complete.call(context, xhr, status)
    triggerGlobal(settings, context, 'ajaxComplete', [xhr, settings])
    ajaxStop(settings)
  }

  // Empty function, used as default callback
  function empty() {}

  $.ajaxJSONP = function(options, deferred){
    if (!('type' in options)) return $.ajax(options)

    var _callbackName = options.jsonpCallback,
      callbackName = ($.isFunction(_callbackName) ?
        _callbackName() : _callbackName) || ('jsonp' + (++jsonpID)),
      script = document.createElement('script'),
      originalCallback = window[callbackName],
      responseData,
      abort = function(errorType) {
        $(script).triggerHandler('error', errorType || 'abort')
      },
      xhr = { abort: abort }, abortTimeout

    if (deferred) deferred.promise(xhr)

    $(script).on('load error', function(e, errorType){
      clearTimeout(abortTimeout)
      $(script).off().remove()

      if (e.type == 'error' || !responseData) {
        ajaxError(null, errorType || 'error', xhr, options, deferred)
      } else {
        ajaxSuccess(responseData[0], xhr, options, deferred)
      }

      window[callbackName] = originalCallback
      if (responseData && $.isFunction(originalCallback))
        originalCallback(responseData[0])

      originalCallback = responseData = undefined
    })

    if (ajaxBeforeSend(xhr, options) === false) {
      abort('abort')
      return xhr
    }

    window[callbackName] = function(){
      responseData = arguments
    }

    script.src = options.url.replace(/\?(.+)=\?/, '?$1=' + callbackName)
    document.head.appendChild(script)

    if (options.timeout > 0) abortTimeout = setTimeout(function(){
      abort('timeout')
    }, options.timeout)

    return xhr
  }

  $.ajaxSettings = {
    // Default type of request
    type: 'GET',
    // Callback that is executed before request
    beforeSend: empty,
    // Callback that is executed if the request succeeds
    success: empty,
    // Callback that is executed the the server drops error
    error: empty,
    // Callback that is executed on request complete (both: error and success)
    complete: empty,
    // The context for the callbacks
    context: null,
    // Whether to trigger "global" Ajax events
    global: true,
    // Transport
    xhr: function () {
      return new window.XMLHttpRequest()
    },
    // MIME types mapping
    // IIS returns Javascript as "application/x-javascript"
    accepts: {
      script: 'text/javascript, application/javascript, application/x-javascript',
      json:   jsonType,
      xml:    'application/xml, text/xml',
      html:   htmlType,
      text:   'text/plain'
    },
    // Whether the request is to another domain
    crossDomain: false,
    // Default timeout
    timeout: 0,
    // Whether data should be serialized to string
    processData: true,
    // Whether the browser should be allowed to cache GET responses
    cache: true
  }

  function mimeToDataType(mime) {
    if (mime) mime = mime.split(';', 2)[0]
    return mime && ( mime == htmlType ? 'html' :
      mime == jsonType ? 'json' :
      scriptTypeRE.test(mime) ? 'script' :
      xmlTypeRE.test(mime) && 'xml' ) || 'text'
  }

  function appendQuery(url, query) {
    if (query == '') return url
    return (url + '&' + query).replace(/[&?]{1,2}/, '?')
  }

  // serialize payload and append it to the URL for GET requests
  function serializeData(options) {
    if (options.processData && options.data && $.type(options.data) != "string")
      options.data = $.param(options.data, options.traditional)
    if (options.data && (!options.type || options.type.toUpperCase() == 'GET'))
      options.url = appendQuery(options.url, options.data), options.data = undefined
  }

  $.ajax = function(options){
    var settings = $.extend({}, options || {}),
        deferred = $.Deferred && $.Deferred(),
        urlAnchor, hashIndex
    for (key in $.ajaxSettings) if (settings[key] === undefined) settings[key] = $.ajaxSettings[key]

    ajaxStart(settings)

    if (!settings.crossDomain) {
      urlAnchor = document.createElement('a')
      urlAnchor.href = settings.url
      // cleans up URL for .href (IE only), see https://github.com/madrobby/zepto/pull/1049
      urlAnchor.href = urlAnchor.href
      settings.crossDomain = (originAnchor.protocol + '//' + originAnchor.host) !== (urlAnchor.protocol + '//' + urlAnchor.host)
    }

    if (!settings.url) settings.url = window.location.toString()
    if ((hashIndex = settings.url.indexOf('#')) > -1) settings.url = settings.url.slice(0, hashIndex)
    serializeData(settings)

    var dataType = settings.dataType, hasPlaceholder = /\?.+=\?/.test(settings.url)
    if (hasPlaceholder) dataType = 'jsonp'

    if (settings.cache === false || (
         (!options || options.cache !== true) &&
         ('script' == dataType || 'jsonp' == dataType)
        ))
      settings.url = appendQuery(settings.url, '_=' + Date.now())

    if ('jsonp' == dataType) {
      if (!hasPlaceholder)
        settings.url = appendQuery(settings.url,
          settings.jsonp ? (settings.jsonp + '=?') : settings.jsonp === false ? '' : 'callback=?')
      return $.ajaxJSONP(settings, deferred)
    }

    var mime = settings.accepts[dataType],
        headers = { },
        setHeader = function(name, value) { headers[name.toLowerCase()] = [name, value] },
        protocol = /^([\w-]+:)\/\//.test(settings.url) ? RegExp.$1 : window.location.protocol,
        xhr = settings.xhr(),
        nativeSetHeader = xhr.setRequestHeader,
        abortTimeout

    if (deferred) deferred.promise(xhr)

    if (!settings.crossDomain) setHeader('X-Requested-With', 'XMLHttpRequest')
    setHeader('Accept', mime || '*/*')
    if (mime = settings.mimeType || mime) {
      if (mime.indexOf(',') > -1) mime = mime.split(',', 2)[0]
      xhr.overrideMimeType && xhr.overrideMimeType(mime)
    }
    if (settings.contentType || (settings.contentType !== false && settings.data && settings.type.toUpperCase() != 'GET'))
      setHeader('Content-Type', settings.contentType || 'application/x-www-form-urlencoded')

    if (settings.headers) for (name in settings.headers) setHeader(name, settings.headers[name])
    xhr.setRequestHeader = setHeader

    xhr.onreadystatechange = function(){
      if (xhr.readyState == 4) {
        xhr.onreadystatechange = empty
        clearTimeout(abortTimeout)
        var result, error = false
        if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304 || (xhr.status == 0 && protocol == 'file:')) {
          dataType = dataType || mimeToDataType(settings.mimeType || xhr.getResponseHeader('content-type'))
          result = xhr.responseText

          try {
            // http://perfectionkills.com/global-eval-what-are-the-options/
            if (dataType == 'script')    (1,eval)(result)
            else if (dataType == 'xml')  result = xhr.responseXML
            else if (dataType == 'json') result = blankRE.test(result) ? null : $.parseJSON(result)
          } catch (e) { error = e }

          if (error) ajaxError(error, 'parsererror', xhr, settings, deferred)
          else ajaxSuccess(result, xhr, settings, deferred)
        } else {
          ajaxError(xhr.statusText || null, xhr.status ? 'error' : 'abort', xhr, settings, deferred)
        }
      }
    }

    if (ajaxBeforeSend(xhr, settings) === false) {
      xhr.abort()
      ajaxError(null, 'abort', xhr, settings, deferred)
      return xhr
    }

    if (settings.xhrFields) for (name in settings.xhrFields) xhr[name] = settings.xhrFields[name]

    var async = 'async' in settings ? settings.async : true
    xhr.open(settings.type, settings.url, async, settings.username, settings.password)

    for (name in headers) nativeSetHeader.apply(xhr, headers[name])

    if (settings.timeout > 0) abortTimeout = setTimeout(function(){
        xhr.onreadystatechange = empty
        xhr.abort()
        ajaxError(null, 'timeout', xhr, settings, deferred)
      }, settings.timeout)

    // avoid sending empty string (#319)
    xhr.send(settings.data ? settings.data : null)
    return xhr
  }

  // handle optional data/success arguments
  function parseArguments(url, data, success, dataType) {
    if ($.isFunction(data)) dataType = success, success = data, data = undefined
    if (!$.isFunction(success)) dataType = success, success = undefined
    return {
      url: url
    , data: data
    , success: success
    , dataType: dataType
    }
  }

  $.get = function(/* url, data, success, dataType */){
    return $.ajax(parseArguments.apply(null, arguments))
  }

  $.post = function(/* url, data, success, dataType */){
    var options = parseArguments.apply(null, arguments)
    options.type = 'POST'
    return $.ajax(options)
  }

  $.getJSON = function(/* url, data, success */){
    var options = parseArguments.apply(null, arguments)
    options.dataType = 'json'
    return $.ajax(options)
  }

  $.fn.load = function(url, data, success){
    if (!this.length) return this
    var self = this, parts = url.split(/\s/), selector,
        options = parseArguments(url, data, success),
        callback = options.success
    if (parts.length > 1) options.url = parts[0], selector = parts[1]
    options.success = function(response){
      self.html(selector ?
        $('<div>').html(response.replace(rscript, "")).find(selector)
        : response)
      callback && callback.apply(self, arguments)
    }
    $.ajax(options)
    return this
  }

  var escape = encodeURIComponent

  function serialize(params, obj, traditional, scope){
    var type, array = $.isArray(obj), hash = $.isPlainObject(obj)
    $.each(obj, function(key, value) {
      type = $.type(value)
      if (scope) key = traditional ? scope :
        scope + '[' + (hash || type == 'object' || type == 'array' ? key : '') + ']'
      // handle data in serializeArray() format
      if (!scope && array) params.add(value.name, value.value)
      // recurse into nested objects
      else if (type == "array" || (!traditional && type == "object"))
        serialize(params, value, traditional, key)
      else params.add(key, value)
    })
  }

  $.param = function(obj, traditional){
    var params = []
    params.add = function(key, value) {
      if ($.isFunction(value)) value = value()
      if (value == null) value = ""
      this.push(escape(key) + '=' + escape(value))
    }
    serialize(params, obj, traditional)
    return params.join('&').replace(/%20/g, '+')
  }
})(Zepto)

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function(){
  // getComputedStyle shouldn't freak out when called
  // without a valid element as argument
  try {
    getComputedStyle(undefined)
  } catch(e) {
    var nativeGetComputedStyle = getComputedStyle;
    window.getComputedStyle = function(element){
      try {
        return nativeGetComputedStyle(element)
      } catch(e) {
        return null
      }
    }
  }
})()

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($){
  $.fn.serializeArray = function() {
    var name, type, result = [],
      add = function(value) {
        if (value.forEach) return value.forEach(add)
        result.push({ name: name, value: value })
      }
    if (this[0]) $.each(this[0].elements, function(_, field){
      type = field.type, name = field.name
      if (name && field.nodeName.toLowerCase() != 'fieldset' &&
        !field.disabled && type != 'submit' && type != 'reset' && type != 'button' && type != 'file' &&
        ((type != 'radio' && type != 'checkbox') || field.checked))
          add($(field).val())
    })
    return result
  }

  $.fn.serialize = function(){
    var result = []
    this.serializeArray().forEach(function(elm){
      result.push(encodeURIComponent(elm.name) + '=' + encodeURIComponent(elm.value))
    })
    return result.join('&')
  }

  $.fn.submit = function(callback) {
    if (0 in arguments) this.bind('submit', callback)
    else if (this.length) {
      var event = $.Event('submit')
      this.eq(0).trigger(event)
      if (!event.isDefaultPrevented()) this.get(0).submit()
    }
    return this
  }

})(Zepto)
