import axios, {AxiosResponse} from 'axios';

import Store from "../store/Store";
import {routeConfig} from "../route/config";

export interface IResult<T> {
    code: number;
    message: string;
    data?: T;
    status: boolean;
}

type TResolve<T> = (value?: (IResult<T> | PromiseLike<IResult<T>> | undefined)) => void;
const callback = {
    then<T>(resolve: TResolve<T>) {
        return (res: AxiosResponse<IResult<T>>) => {
            const data = res.data;
            resolve(data)
            if (data.message.startsWith('validate/token ')) {
                Store.clear();
                routeConfig.history.push(routeConfig.names.login);
            }
        };
    },
    catch<T>(resolve: TResolve<T>) {
        return (error: any) => {
            console.log(error);
            resolve({
                code: 4444,
                message: '网络错误',
                status: false
            });
        }
    }
}


export type FormData = { [key: string]: string };

export const http = {
    retrieveHeaders: () => {
        return {headers: {token: Store.store.token}}
    },
    get<T>(url: string) {
        return async () => {
            return new Promise<IResult<T>>(resolve => {
                axios.get<IResult<T>>(url, this.retrieveHeaders())
                    .then(callback.then(resolve))
                    .catch(callback.catch(resolve));
            });
        };
    },
    delete<T>(url: string) {
        return async () => {
            return new Promise<IResult<T>>(resolve => {
                axios.delete<IResult<T>>(url, this.retrieveHeaders())
                    .then(callback.then(resolve))
                    .catch(callback.catch(resolve));
            });
        };
    },
    post<T>(url: string) {
        return async (data?: any) => {
            return new Promise<IResult<T>>(resolve => {
                axios.post<IResult<T>>(url, data, this.retrieveHeaders())
                    .then(callback.then(resolve))
                    .catch(callback.catch(resolve));
            });
        };
    },
    put<T>(url: string) {
        return async (data: any) => {
            return new Promise<IResult<T>>(resolve => {
                axios.put<IResult<T>>(url, data, this.retrieveHeaders())
                    .then(callback.then(resolve))
                    .catch(callback.catch(resolve));
            });
        };
    },
}

export const group = `
create table wb_group (
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    order_no int not null,
    weights int not null,
    name varchar(32) unique not null,
    menu_ids varchar (192) not null comment 'join with -'
) charset = utf8 engine = InnoDB;
`;

export const role_table = `
create table wb_role (
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    group_id int not null,
    order_no int not null,
    weights int not null,
    name varchar(32) unique not null,
    api_ids varchar (192) not null comment 'join with -'
) charset = utf8 engine = InnoDB;
`;

export const user_table = `
create table wb_user(
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    role_id int not null,
    password char(96) not null,
    access_id int not null,
    login_time int not null,
    token_expires_time int not null,
    serial char(32) unique not null ,
    serial_expires_time int not null ,
    username varchar(64) unique not null,
    email varchar(64) unique not null
) charset = utf8 engine = InnoDB;
`;

export const menu_table = `
create table wb_menu (
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    order_no int not null comment 'show order No.',
    weights int not null,
    path varchar(64) not null comment 'client route path',
    name varchar (32) unique not null comment 'client route name'
) charset = utf8 engine = InnoDB;
`;

export const api_table = `
create table wb_api (
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    order_no int not null,
    weights int not null,
    name varchar(32) not null,
    map_path varchar(128) unique not null,
    api_ids varchar (192) not null comment 'join with -'
) charset = utf8 engine = InnoDB;
`;


export const access_table = `
create table wb_access(
    id int primary key auto_increment,
    create_time  timestamp,
    expires_time int not null,
    count int not null,
    ip char(15) unique not null
) charset = utf8 engine = InnoDB;
`;

export const history_table = `
create table history (
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    user_id int not null,
    accuracy int not null comment 'correct%',
    amount int not null comment 'total',
    rate int not null comment 'n/min',
    cost time not null comment 'seconds',
    errors varchar(128) comment 'error words, join with -'
) charset = utf8 engine = InnoDB;
`;

export const word_table = `
create table word (
    id int primary key auto_increment,
    create_time  timestamp,
    status enum('1', '0'),
    chinese  char(1) unique,
    serial  char(5) comment 'code table of chinese'
) charset = utf8 engine = InnoDB;
`;