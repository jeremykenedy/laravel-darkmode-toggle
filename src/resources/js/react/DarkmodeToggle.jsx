import { useCallback, useEffect, useRef, useState } from 'react'

const modes = ['light', 'dark', 'system']

const defaultLabels = { light: 'Light', dark: 'Dark', system: 'System' }

function read(storageKey) {
    try {
        return localStorage.getItem(storageKey)
    } catch (error) {
        return null
    }
}

export default function DarkmodeToggle({
    defaultMode = 'system',
    storageKey = 'theme',
    persistUrl = '',
    persistMethod = 'PUT',
    persistField = 'dark_mode',
    className = 'dark',
    dataAttribute = '',
    colorScheme = false,
    toggleLabel = 'Toggle theme',
    labels = defaultLabels,
}) {
    const [open, setOpen] = useState(false)
    const [current, setCurrent] = useState(defaultMode)
    const root = useRef(null)

    const apply = useCallback((mode) => {
        const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)

        document.documentElement.classList.toggle(className, isDark)

        if (dataAttribute) {
            document.documentElement.setAttribute(dataAttribute, isDark ? 'dark' : 'light')
        }

        if (colorScheme) {
            document.documentElement.style.colorScheme = isDark ? 'dark' : 'light'
        }
    }, [className, dataAttribute, colorScheme])

    useEffect(() => {
        const stored = read(storageKey) || defaultMode

        setCurrent(stored)
        apply(stored)

        const query = window.matchMedia('(prefers-color-scheme: dark)')
        const onSystemChange = () => {
            if ((read(storageKey) || defaultMode) === 'system') {
                apply('system')
            }
        }
        const onDocumentClick = (event) => {
            if (root.current && !root.current.contains(event.target)) {
                setOpen(false)
            }
        }

        query.addEventListener('change', onSystemChange)
        document.addEventListener('click', onDocumentClick)

        return () => {
            query.removeEventListener('change', onSystemChange)
            document.removeEventListener('click', onDocumentClick)
        }
    }, [apply, defaultMode, storageKey])

    function setTheme(mode) {
        setCurrent(mode)

        try {
            localStorage.setItem(storageKey, mode)
        } catch (error) {
            // Storage can be unavailable in private windows, the theme still applies.
        }

        apply(mode)
        setOpen(false)

        if (persistUrl) {
            fetch(persistUrl, {
                method: persistMethod,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ [persistField]: mode }),
            }).catch(() => {})
        }
    }

    return (
        <div className="relative" ref={root} onKeyDown={(event) => event.key === 'Escape' && setOpen(false)}>
            <button
                type="button"
                aria-expanded={open}
                aria-haspopup="true"
                aria-label={toggleLabel}
                title={toggleLabel}
                onClick={() => setOpen(!open)}
            >
                {current === 'light' && <svg className="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>}
                {current === 'dark' && <svg className="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>}
                {current === 'system' && <svg className="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>}
            </button>

            {open && (
                <div role="menu" className="absolute right-0 mt-1 w-36 rounded-lg bg-white dark:bg-gray-800 shadow-lg z-50">
                    {modes.map((mode) => (
                        <button
                            key={mode}
                            type="button"
                            role="menuitemradio"
                            aria-checked={current === mode}
                            className={`block w-full px-4 py-2 text-sm text-left ${current === mode ? 'font-bold' : ''}`}
                            onClick={() => setTheme(mode)}
                        >
                            {labels[mode]}
                        </button>
                    ))}
                </div>
            )}
        </div>
    )
}
