export function PageLoading() {
    return (
        <div className="flex flex-col items-center justify-center h-64 gap-2">
        <progress className="progress progress-primary w-lg" />
        <span className="text-sm text-gray-500">Fetching data, please wait...</span>
        </div>
    )
}