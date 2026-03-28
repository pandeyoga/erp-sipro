import { useEffect, useState } from "react";

interface ProtectedImageProps {
  label?: string;
  imageUrl: string;
  linkOnly?: boolean;
}



const ProtectedImage: React.FC<ProtectedImageProps> = ({
  label,
  imageUrl,
  linkOnly,
}) => {
  const [blobUrl, setBlobUrl] = useState<string | null>(null);
  const [previewUrl, setPreviewUrl] = useState<string | null>(null);

  useEffect(() => {
    const loadImage = async () => {
      // Ubah http → https jika diperlukan
      if (imageUrl.startsWith("http://")) {
        imageUrl = imageUrl.replace("http://", "https://");
      }
      const token = localStorage.getItem("access_token");

      if (!token) return;

      const res = await fetch(imageUrl, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (!res.ok) {
        console.error("Gagal mengambil gambar");
        return;
      }

      const blob = await res.blob();
      const url = URL.createObjectURL(blob);
      setBlobUrl(url);
    };

    loadImage();
  }, [imageUrl]);

  if (!blobUrl) return <p>Mengambil gambar...</p>;

  return (
    <div className="">
      {linkOnly ? (
        // <button
        //     onClick={() => window.open(blobUrl, '_blank')}
        //     className="btn btn-info btn-sm my-1"
        // >
        //     Lihat Dokumen
        // </button>
        <button
          onClick={() => setPreviewUrl(blobUrl)}
          className="btn btn-info btn-sm"
        >
          Lihat Dokumen
        </button>
      ) : (
        <a href={blobUrl} target="_blank" rel="noopener noreferrer">
          <img
            src={blobUrl}
            alt="Preview"
            className="max-h-64 w-full rounded border object-contain hover:opacity-90 transition"
          />
        </a>
      )}
      {previewUrl && (
        <div className="fixed inset-0 bg-black/80 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg w-full max-w-4xl h-[80vh] relative shadow-lg flex flex-col overflow-hidden">
            {/* Header */}
            <div className="flex items-center justify-between px-4 py-3 border-b">
              <h2 className="text-lg font-semibold text-gray-800">{label}</h2>
              <button
                onClick={() => setPreviewUrl(null)}
                className="text-gray-600 hover:text-black text-xl"
              >
                ✕
              </button>
            </div>

            {/* Iframe content */}
            <div className="flex-1">
              <iframe
                src={previewUrl ?? ""}
                className="w-full h-full"
                frameBorder="0"
              />
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ProtectedImage;
