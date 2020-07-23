import {routeConfig} from "../../../route/config";


export interface ILinkItem {
    path: string;
    text: string;
}


const link = {
    login: {
        path: routeConfig.names.login,
        text: '登录帐号'
    },
    password: {
        path: routeConfig.names.password,
        text: '找回密码'
    },
    register: {
        path: routeConfig.names.register,
        text: '注册帐号'
    }
};

export default link;