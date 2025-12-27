const MiniReact = (() => {
    let hooks = [];
    let hookIndex = 0;
    let rootEl = null;
    let rootComponent = null;

    // Event delegation storage
    const eventHandlers = {
        click: {},
        input: {},
        change: {},
    };

    function useState(initialValue) {
        const idx = hookIndex;
        hooks[idx] = hooks[idx] ?? initialValue;

        function setState(newValue) {
            hooks[idx] =
                typeof newValue === "function"
                    ? newValue(hooks[idx])
                    : newValue;
            render(rootComponent, rootEl);
        }

        hookIndex++;
        return [hooks[idx], setState];
    }

    function useEffect(callback, deps) {
        const idx = hookIndex;
        const oldDeps = hooks[idx];
        let changed = true;

        if (oldDeps) {
            changed = deps.some((d, i) => d !== oldDeps[i]);
        }

        if (changed) callback();
        hooks[idx] = deps;
        hookIndex++;
    }

    function render(component, container) {
        rootEl = container;
        rootComponent = component;
        hookIndex = 0;
        container.innerHTML = component();
    }

    // Helper: traverse up to find matching selector
    function matchesSelectorUp(element, selector) {
        while (element && element !== document) {
            if (element.matches(selector)) return element;
            element = element.parentElement;
        }
        return null;
    }

    // Event delegation
    function delegateEvent(type) {
        document.addEventListener(type, (e) => {
            Object.entries(eventHandlers[type]).forEach(
                ([selector, handler]) => {
                    const match = matchesSelectorUp(e.target, selector);
                    if (match) {
                        const wrappedEvent = new Proxy(e, {
                            get(target, prop) {
                                if (prop === "currentTarget") return match; // React-like currentTarget
                                return Reflect.get(target, prop);
                            },
                        });
                        handler(wrappedEvent);
                    }
                }
            );
        });
    }

    ["click", "input", "change"].forEach(delegateEvent);

    // onClick works for any selector
    function onClick(selector, handler) {
        eventHandlers.click[selector] = handler;
    }

    // onChange works for any selector
    function onChange(selector, handler) {
        eventHandlers.input[selector] = handler; // input events
        eventHandlers.change[selector] = handler; // select, checkbox, etc.
    }

    // Query helpers
    function element(selector) {
        if (selector.startsWith("#"))
            return document.getElementById(selector.slice(1));
        return document.querySelector(selector);
    }
    function elementQuery(selector) {
        return document.querySelector(selector);
    }

    function elementAll(selector, callback) {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el, i) => callback(el, i));
    }
    function onElementAllEvent(selector, handler, eventType = "click") {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el, i) => {
            // Avoid adding multiple listeners
            el.removeEventListener(eventType, el._miniReactHandler);

            const wrappedHandler = (e) => handler(e, i, el, elements);
            el._miniReactHandler = wrappedHandler; // store reference
            el.addEventListener(eventType, wrappedHandler);
        });
    }
    function onElementAllParentEvent(
        parentSelector,
        childSelector,
        handler,
        eventType = "click"
    ) {
        const parent = document.querySelector(parentSelector);
        if (!parent) return;

        parent.addEventListener(eventType, (e) => {
            if (e.target.closest(childSelector)) {
                handler(e, e.target.closest(childSelector));
            }
        });
    }

    return {
        useState,
        useEffect,
        render,
        onClick,
        onChange,
        element,
        elementAll,
        onElementAllEvent,
        onElementAllParentEvent,
        elementQuery,
    };
})();
