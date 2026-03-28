// components/AuthForm.tsx
"use client"

import { useForm } from "react-hook-form"
import { z } from "zod"
import { zodResolver } from "@hookform/resolvers/zod"
import { useState } from "react"
import { Eye, EyeOff, Loader2 } from "lucide-react"
import { login } from "@/api/auth/auth"
import toast from "react-hot-toast"
import { useRouter } from "next/navigation"
import { jwtDecode } from "jwt-decode"
import axios from "axios"
import { useTranslations } from "next-intl"

const schema = z.object({
  email: z.string().email("Email tidak valid"),
  password: z.string().min(6, "Minimal 6 karakter"),
})

type FormData = z.infer<typeof schema>

export interface MyJwtPayload {
  user: {
    id: string
    name: string
    email: string
    role_id: string
    role_name: string
    permissions: string[]
  }
  exp: number
  iat: number
}

export default function AuthForm() {
  const t = useTranslations('auth');
  const router = useRouter()
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
  })

  const [showPassword, setShowPassword] = useState(false)
  const [loading, setLoading] = useState(false)

  
  const onSubmit = async (data: FormData) => {
    setLoading(true)
    try {
      const response = await login(data)
  
      if (response?.success) {
        const token = response.data.access_token
        const permissions = response.data.permissions
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
        localStorage.setItem("permissions", JSON.stringify(permissions))
        localStorage.setItem("token_expires_in_minutes", durationMinutes.toString())
        
        // Logout otomatis setelah `delay`
        setTimeout(() => {
          localStorage.clear()
          toast.error("Sesi Anda telah berakhir. Silakan login kembali.")
          router.push("/login")
        }, delay)
        
        router.push("/dashboard")
      }
      setLoading(false)
    } catch (error: any) {
      setLoading(false)
      if (axios.isAxiosError(error)) {
        const apiError = error?.response?.data
        if (apiError?.errors) {
          let messages = Object.values(apiError.errors).flat().join(', ')
          toast.error(messages)
        }else{
          let messages = apiError.message
          messages = messages == "Unauthorized" ? t('invalid_credentials') : messages
          toast.error(messages)
        }
      }
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="w-full space-y-4">
      {/* Email */}
      <fieldset className="form-control w-full">
        <label className="label">
          <span className="label-text">{t('email')}</span>
        </label>
        <input
          type="text"
          placeholder={t('enter_password')}
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
          <span className="label-text">{t('password')}</span>
        </label>
        <div className="relative">
          <input
            type={showPassword ? "text" : "password"}
            placeholder={t('enter_password')}
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

      <button type="submit" className="btn btn-primary w-full" disabled={loading}>
      {loading ? (
    <>
      <Loader2 className="animate-spin w-4 h-4" />
      Logging in...
    </>
  ) : (
    "Login"
  )}
      </button>
    </form>
  )
}
