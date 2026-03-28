import { useState, useEffect, useRef } from 'react'

function apply(mode, cls = 'dark') {
    const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
    document.documentElement.classList.toggle(cls, isDark)
}

export default function DarkmodeToggle({ defaultMode = 'system', storageKey = 'theme', persistUrl = '', persistMethod = 'PUT', persistField = 'dark_mode', className = 'dark' }) {
    const [open, setOpen] = useState(false)
    const [current, setCurrent] = useState(defaultMode)
    const ref = useRef(null)

    useEffect(() => {
        const stored = localStorage.getItem(storageKey)
        if (stored) setCurrent(stored)
        apply(stored || defaultMode, className)
        const handler = e => { if (ref.current && !ref.current.contains(e.target)) setOpen(false) }
        document.addEventListener('mousedown', handler)
        return () => document.removeEventListener('mousedown', handler)
    }, [])

    function setTheme(mode) {
        setCurrent(mode)
        localStorage.setItem(storageKey, mode)
        apply(mode, className)
        setOpen(false)
        if (persistUrl) {
            fetch(persistUrl, {
                method: persistMethod,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content, 'Accept': 'application/json' },
                body: JSON.stringify({ [persistField]: mode }),
            }).catch(() => {})
        }
    }

    const options = ['light', 'dark', 'system']

    return (
        <div className="relative" ref={ref}>
            <button onClick={() => setOpen(!open)} type="button" title="Toggle theme">
                {current === 'light' && <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>}
                {current === 'dark' && <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>}
                {current === 'system' && <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>}
            </button>
            {open && (
                <div className="absolute right-0 mt-1 w-36 rounded-lg bg-white dark:bg-gray-800 shadow-lg z-50">
                    {options.map(opt => (
                        <button key={opt} onClick={() => setTheme(opt)} className={`block w-full px-4 py-2 text-sm text-left ${current === opt ? 'font-bold' : ''}`}>
                            {opt.charAt(0).toUpperCase() + opt.slice(1)}
                        </button>
                    ))}
                </div>
            )}
        </div>
    )
}
