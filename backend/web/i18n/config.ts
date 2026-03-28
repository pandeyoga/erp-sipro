import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import translationEN from './../public/locales/en/common.json';
import translationID from './../public/locales/id/common.json';
import HttpBackend from 'i18next-http-backend'
import { getOptions } from './settings';

i18n
  .use(HttpBackend)
  .use(initReactI18next)
  .init({
    ...getOptions(),
    // debug: process.env.NODE_ENV === 'development',
    // resources: {
    //   en: { translation: translationEN },
    //   id: { translation: translationID },
    // },
    // interpolation: {
    //   escapeValue: false,
    // },
    backend: {
      loadPath: '/locales/{{lng}}/{{ns}}.json',
    },
  });

export default i18n;
