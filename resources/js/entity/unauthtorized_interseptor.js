import axios from 'axios';
import { Promise } from "es6-promise";
import { router } from './router';

export default () => {

    axios.interceptors.response.use( (response) => {
        // Return a successful response back to the calling service
        return response;
    }, (error) => {
        // Return any error which is not due to authentication back to the calling service
        if (error.response.status !== 401) {
            return new Promise((resolve, reject) => {
                reject(error);
            });
        }

        if (error.config.url === '/api/token/refresh') {
            router.push({ name: 'login' });

            return new Promise((resolve, reject) => {
                reject(error);
            });
        }
    });
}
