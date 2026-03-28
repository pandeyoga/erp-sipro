export const getProtectedImageUrl = async (imageUrl : string) => {
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
  return url
};