import axios from "axios";
import Router from "next/router"; // gunakan Router dari Next.js
import toast from "react-hot-toast";

const axiosInstance = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || "https://dev-iremp-crm.irondevlab.com/api",
  headers: {
    "Content-Type": "application/json",
    'Cache-Control': 'no-cache, no-store, must-revalidate',
    'Pragma': 'no-cache',
    'Expires': '0',
    Accept: "application/json",
  },
  withCredentials: true, // opsional jika pakai cookie/session
});

axiosInstance.interceptors.request.use((config) => {
    const token = typeof window !== "undefined" ? localStorage.getItem("access_token") : null
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (axios.isAxiosError(error)) {
      const apiError = error?.response?.data
      console.log(apiError)
      if (apiError?.errors) {
        const messages = Object.values(apiError.errors).flat().join(', ')
        toast.error(messages)
      }
    }
    
    if (typeof window !== "undefined") {
      const status = error.response?.status;

      if (status === 401 || status === 403) {
        // localStorage.removeItem("access_token");
      }
    }

    return Promise.reject(error);
  }
);
  

export default axiosInstance;
