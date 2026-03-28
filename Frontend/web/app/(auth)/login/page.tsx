// import AuthForm from "@/components/auth/AuthForm";
// import AuthHeader from "@/components/auth/AuthHeader";
import AuthForm from "@/components/auth/AuthForm";
import { useTranslations } from "next-intl";

export default function LoginPage() {
  const t = useTranslations('auth');
  
  return (
    <div className="min-h-screen flex">

      {/* Right Form */}
      <div className="w-full md:w-1/2 flex items-center justify-center p-8 bg-base-100 mx-auto">
        <div className="w-full max-w-md gap-4">
            <img
                src="/assets/logo.png"
                alt="Login Illustration"
                className="mb-4 w-3/4 mx-auto"
              />
            <div className="text-center mb-6">
                <h1 className="text-xl font-bold text-gray-600">{t('welcome_back')} 👋</h1>
                <p className="text-sm text-gray-500 mt-2">
                {t('enter_credential')}
                </p>
            </div>
          <AuthForm />
          <div className="mt-4 text-center">
            {/* <a href="/forgot-password" className="text-sm text-primary hover:underline" >
              {t('forgot_password')}
            </a> */}
            {/* <p className="mt-2 text-sm" >
              {t('dont_have_account')}
              <a href="/register" className="text-primary hover:underline" >
              {t('register')}
              </a>
            </p> */}
          </div>
        </div>
      </div>
    </div>
  );
}
