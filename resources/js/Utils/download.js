export function downloadFile(data, filename, mimeType = null) {
    const blob = data instanceof Blob ? data : new Blob([data], { type: mimeType })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)

    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()

    setTimeout(() => {
        document.body.removeChild(link)
        URL.revokeObjectURL(url)
    }, 100)
}
