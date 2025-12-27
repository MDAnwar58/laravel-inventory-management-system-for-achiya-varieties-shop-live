// --- your hook implementations ---
function createUseState() {
    let state;

    function useState(initialValue) {
        if (state === undefined) state = initialValue;

        function setState(newValue) {
            if (typeof newValue === "function") state = newValue(state);
            else state = newValue;
        }
        let s = {
            get value() {
                return state;
            },
        };

        // return a proxy object so updates reflect globally
        return [
            {
                get value() {
                    return state;
                },
            },
            setState,
        ];
    }

    return useState;
}

function createUseEffect() {
    let lastDeps;

    function useEffect(callback, deps) {
        let hasChanged = true;

        if (deps) {
            if (lastDeps) {
                hasChanged = deps.some((dep, i) => dep !== lastDeps[i]);
            }
            lastDeps = [...deps];
        }

        if (hasChanged) callback();
    }

    return useEffect;
}

function createUseCallback() {
    let lastCallback;
    let lastDeps;

    function useCallback(callback, deps) {
        const hasNoDeps = !deps;
        const hasChangedDeps = lastDeps
            ? !deps.every((dep, i) => dep === lastDeps[i])
            : true;

        if (hasNoDeps || hasChangedDeps) {
            lastCallback = callback;
            lastDeps = deps;
        }

        return lastCallback;
    }

    return useCallback;
}

// --- event helpers ---
function createUseOnClick() {
    function onClick(selector, handler) {
        const el = document.querySelector(selector);
        if (!el) return;

        if (el._clickHandler) el.removeEventListener("click", el._clickHandler);

        el._clickHandler = handler;
        el.addEventListener("click", handler);
    }

    return onClick;
}

function createUseOnChange() {
    function onChange(selector, handler) {
        const el = document.querySelector(selector);
        if (!el) return;

        if (el._changeHandler) {
            el.removeEventListener("input", el._changeHandler);
            el.removeEventListener("change", el._changeHandler);
        }

        el._changeHandler = (e) => handler(e.target.value, e);

        el.addEventListener("input", el._changeHandler);
        el.addEventListener("change", el._changeHandler);
    }

    return onChange;
}

// --- DOM helpers ---
function createUseElement() {
    function element(selector) {
        return selector.startsWith("#")
            ? document.getElementById(selector)
            : document.querySelector(selector);
    }

    return element;
}

function createUseElementAll() {
    function elementAll(selector, callback) {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el, i) => callback(el.value, el, i, elements));
    }

    return elementAll;
}

// --- global object ---
const ReactHook = {
    useState: createUseState(),
    useEffect: createUseEffect(),
    useCallback: createUseCallback(),
    onClick: createUseOnClick(),
    onChange: createUseOnChange(),
    element: createUseElement(),
    elementAll: createUseElementAll(),
};
