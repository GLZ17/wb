import {IStoreData} from "../store/Store";
import {http, FormData} from './db_table';

const sign = {
    login: (data?: FormData) => {
        return http.post<IStoreData>('/api/login')(data);
    },
    register: (data: FormData) => {
        return http.post<IStoreData>('/api/register')(data);
    },
    logout: () => {
        return http.post<string>('/api/logout')();
    },
    appeal: (data: FormData) => {
        return http.post<string>('/api/password/appeal')(data);
    },
    retrieve: (data: FormData) => {
        return http.post<string>('/api/password/retrieve')(data);
    }
};

export default sign;