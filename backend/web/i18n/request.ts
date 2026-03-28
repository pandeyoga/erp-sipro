import { getCookie } from 'cookies-next';
import {getRequestConfig} from 'next-intl/server';
 
export default getRequestConfig(async () => {
  const locale = await getCookie("locale") ?? "en";
 
  return {
    locale,
    messages: (await import(`../public/locales/${locale}/common.json`)).default
  };
});