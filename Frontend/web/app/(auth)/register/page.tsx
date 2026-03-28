import RegisterForm from "@/components/auth/RegisterForm";
import { useTranslations } from "next-intl";

export default function RegisterPage() {
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
          <RegisterForm />
          <div className="mt-4 text-center">
            <a href="/forgot-password" className="text-sm text-primary hover:underline">
            {t('forgot_password')}
            </a>
            <p className="mt-2 text-sm">
            {t('already_have_account')}{" "}
              <a href="/login" className="text-primary hover:underline">
                Login
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
