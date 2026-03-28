// components/AuthForm.tsx
"use client"

import { useForm } from "react-hook-form"
import { z } from "zod"
import { zodResolver } from "@hookform/resolvers/zod"
import { useState } from "react"
import { Eye, EyeOff } from "lucide-react"
import { login } from "@/api/auth/auth"
import toast from "react-hot-toast"
import { useRouter } from "next/navigation"
import { jwtDecode } from "jwt-decode"
import { MyJwtPayload } from "./AuthForm"

const schema = z.object({
  fullname : z.string(),
  email: z.string().email("Email tidak valid"),
  password: z.string().min(6, "Minimal 6 karakter"),
  password_confirmation : z.string().min(6, "Minimal 6 karakter"),
})

type FormData = z.infer<typeof schema>

export default function RegisterForm() {
  const router = useRouter()
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
  })

  const [showPassword, setShowPassword] = useState(false)

  
  const onSubmit = async (data: FormData) => {
    const toastId = toast.loading("Logging in...")
  
    try {
      const response = await login(data)
  
      if (response?.success) {
        const token = response.data.access_token
        const decoded = jwtDecode(token) as MyJwtPayload
        
        // Ambil nama dan role_id dari payload user
        const name = decoded?.user?.name
        const roleId = decoded?.user?.role_id

        // Hitung waktu kedaluwarsa (dalam detik atau menit)
        const issuedAt = decoded.iat ?? 0
        const expiredAt = decoded.exp ?? 0
        const durationSeconds = expiredAt - issuedAt
        const durationMinutes = Math.floor(durationSeconds / 60)

        const now = Math.floor(Date.now() / 1000)
        const delay = (expiredAt - now) * 1000 // dalam ms

        // Simpan ke localStorage
        localStorage.setItem("access_token", token)
        localStorage.setItem("fullname", name || "")
        localStorage.setItem("role_id", roleId || "")
        localStorage.setItem("token_expires_in_minutes", durationMinutes.toString())
        
        // Logout otomatis setelah `delay`
        setTimeout(() => {
          localStorage.clear()
          toast.error("Sesi Anda telah berakhir. Silakan login kembali.")
          router.push("/login")
        }, delay)
        
        toast.success("Login berhasil!", { id: toastId })
        router.push("/dashboard")
      } else {
        toast.error(response?.message || "Login gagal", { id: toastId })
      }
    } catch (error: any) {
      toast.error(error?.response?.data?.message || "Terjadi kesalahan saat login", {
        id: toastId,
      })
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="w-full space-y-4">
      {/* Fullname */}
      <fieldset className="form-control w-full">
        <label className="label">
          <span className="label-text">Fullname</span>
        </label>
        <input
          type="text"
          placeholder="Enter your fullname"
          className="input input-bordered w-full"
          {...register("fullname")}
        />
        {errors.fullname && (
          <span className="text-red-500 text-sm mt-1">{errors.fullname.message}</span>
        )}
      </fieldset>

      {/* Email */}
      <fieldset className="form-control w-full">
        <label className="label">
          <span className="label-text">Email</span>
        </label>
        <input
          type="text"
          placeholder="Enter your email"
          className="input input-bordered w-full"
          {...register("email")}
        />
        {errors.email && (
          <span className="text-red-500 text-sm mt-1">{errors.email.message}</span>
        )}
      </fieldset>

      {/* Password */}
      <fieldset className="form-control w-full">
        <label className="label">
          <span className="label-text">Password</span>
        </label>
        <div className="relative">
          <input
            type={showPassword ? "text" : "password"}
            placeholder="Enter your password"
            className="input input-bordered w-full pr-12"
            {...register("password")}
          />
          <button
            type="button"
            onClick={() => setShowPassword((prev) => !prev)}
            className="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500"
          >
            {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
          </button>
        </div>
        {errors.password && (
          <span className="text-red-500 text-sm mt-1">{errors.password.message}</span>
        )}
      </fieldset>

      {/* Fullname */}
      <fieldset className="form-control w-full">
        <label className="label">
          <span className="label-text">Password Confirmation</span>
        </label>
        <input
          type="text"
          placeholder="Retype Your password"
          className="input input-bordered w-full"
          {...register("password_confirmation")}
        />
        {errors.password_confirmation && (
          <span className="text-red-500 text-sm mt-1">{errors.password_confirmation.message}</span>
        )}
      </fieldset>

      <button type="submit" className="btn btn-primary w-full">
        Register
      </button>
    </form>
    
  )
}
