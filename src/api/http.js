import axios from 'axios'

export const API = axios.create({
    baseURL: 'http://stateless.kadastrcard.ru/api/'
});
