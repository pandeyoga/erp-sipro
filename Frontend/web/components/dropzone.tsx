import { useEffect, useMemo, useState } from 'react';
import type { UseFormSetValue } from 'react-hook-form';
import { useDropzone, Accept, FileWithPath } from 'react-dropzone';
import ProtectedImage from './protected-image';

interface FileDropzoneProps {
  label: string;
  name: string;
  setValue: UseFormSetValue<any>;
  accept?: Accept;
  maxSize?: number;
  multiple?: boolean;
  className ?: string;
  initialPreviewSrc?: string | any;     // URL eksternal jika ada
  initialFileName?: string;       // Nama file eksternal (untuk PDF atau selain gambar)
}

const FileDropzone: React.FC<FileDropzoneProps> = ({
  label,
  name,
  setValue,
  accept = {
    'application/pdf': ['.pdf','.xlsx','.docx'],
    'image/*': ['.png', '.jpg', '.jpeg'],
  },
  multiple = false,
  maxSize = 10,
  initialPreviewSrc,
  initialFileName,
  className='mt-4'
}) => {
  const {
    getRootProps,
    getInputProps,
    acceptedFiles,
  } = useDropzone({
    accept,
    multiple,
    maxSize : maxSize * 1024 * 1024,
    onDrop: (acceptedFiles: FileWithPath[]) => {
      setValue(name, multiple ? acceptedFiles : acceptedFiles[0], { shouldValidate: true });
    },
  });

  // const [externalPreview, setExternalPreview] = useState<string | null>(initialPreviewSrc ?? null);

  useEffect(() => {
    if (acceptedFiles.length > 0) {
      setValue(name, multiple ? acceptedFiles : acceptedFiles[0], { shouldValidate: true });
      // setExternalPreview(null); // Hapus preview eksternal saat file baru dipilih
    }
  }, [acceptedFiles, setValue, name, multiple]);

  const previews = useMemo(() => {
    return acceptedFiles.map((file) => {
      const isImage = file.type.startsWith('image/');
      const previewUrl = URL.createObjectURL(file);
      return (
        <div key={file.name} className="mt-2">
          {isImage ? (
            <img
              src={previewUrl}
              alt={file.name}
              className="max-h-64 rounded border object-contain"
              onLoad={() => URL.revokeObjectURL(previewUrl)}
            />
          ) : (
            <p className="text-gray-700 font-medium">
              📄 {file.name}
            </p>
          )}
        </div>
      );
    });
  }, [acceptedFiles]);

  const { refKey, ...inputProps } = getInputProps();

  return (
    <div className={"form-control "+className}>
      <label className="label">
        <span className="label-text">{label}</span>
      </label>
      

      <div
        {...getRootProps()}
        className="border border-dashed border-gray-400 p-6 rounded-lg text-center cursor-pointer hover:bg-base-200 transition overflow-scroll"
      >
        <input {...inputProps} />
        <p className="text-gray-500">
          {acceptedFiles.length == 0 && 'Drag & drop file here, or click to select'}
        </p>
        

        {/* External Preview (Hanya jika belum ada file baru) */}
        {acceptedFiles.length === 0 && initialPreviewSrc && typeof initialPreviewSrc == "string" && (
          <div className="mt-4">
            
            {initialPreviewSrc.match(/\.(jpeg|jpg|png|gif|webp)$/i) ? (
              <ProtectedImage imageUrl={initialPreviewSrc} />
            ) : (
              <p className="text-sm text-gray-700 font-medium">
                📄 {initialFileName || 'File tersedia'}
              </p>
            )}
          </div>
        )}

        {/* Local File Previews */}
        {acceptedFiles.length > 0 && (
          <div className="mt-4 space-y-2 max-h-64 w-full mx-auto">{previews}</div>
        )}
      </div>
      <label className="label">
        <span className="label-text">{toHumanReadableSentence(accept)}. Max : {maxSize} MB</span>
      </label>
    </div>
  );
};

type MimeMap = Record<string, string[]>


function toHumanReadableSentence(map: MimeMap | any): string {
  const mimeLabels: Record<string, string> = {
    "application/pdf": "File PDF",
    "image/*": "Gambar",
    "application/msword": "Dokumen Word",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
      "Dokumen Word (docx)",
    "application/vnd.ms-excel": "Excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
      "Excel (xlsx)",
  }

  const parts = Object.entries(map).map(([mime, exts]) => {
    const label = mimeLabels[mime] || mime
    return `${label} (${(exts as string[]).join(", ")})`
  })

  if (parts.length > 1) {
    return parts.slice(0, -1).join(", ") + " dan " + parts.slice(-1)
  } else {
    return parts[0] || ""
  }
}



export default FileDropzone;
