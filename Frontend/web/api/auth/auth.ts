import axios from "@/lib/axios"
import { setCookie, getCookie, deleteCookie} from "cookies-next"

type LoginBody = {
  email: string
  password: string
}

type CheckPermissionsBody = {
  permissions: string[]
}

export const login = async (data: LoginBody) => {
  const response = await axios.post("/login", data)
  // contoh: kalau server ngirim token di response
  console.log(response.data)
  if (response.data.data.access_token) {
    setCookie("access_token", response.data.data.access_token, {
      maxAge: 60 * 60 * 24 * 7, // 7 hari
      path: "/",
    })
  }

  if (response.data.data.permissions) {
    setCookie("permissions", JSON.stringify(response.data.data.permissions), {
      maxAge: 60 * 60 * 24 * 7, // 7 hari
      path: "/",
    })
  }
  return response.data
}

export const isAllowed = async (permission : string) : Promise<boolean> => {
  try{
    const permissions : string  = await getCookie("permissions") ?? ""
    const permissions_data = JSON.parse(permissions)
    const codes = permissions_data.map((p: any) => p.code)
    if(codes[0] == 'all_access') return true
    return codes.includes(permission)
  }catch(e){
    return false
  }
}

export const toggleLanguageApi = async (lang : string) => {
  setCookie("locale", lang, {
    maxAge: 60 * 60 * 24 * 7, // 7 hari
    path: "/",
  })
}



export const refreshToken = async () => {
  const response = await axios.post("/refresh")
  return response.data
}

export const logout = async () => {
  deleteCookie("access_token")
  deleteCookie("permissions")
  const response = await axios.delete("/logout")
  return response.data
}

export const checkPermissions = async (data: CheckPermissionsBody) => {
  const response = await axios.post("/check-permissions", data)
  return response.data
}
