import React from 'react';
import Form, {
    IFormField,
    IFormApi,
    createFormField,
    FormResult,
    IInputAttributes
} from './form/Form';

import link from './form/link';
import {bindValidator} from './form/Validate';
import {routeConfig} from "../../route/config";
import {apiRequest} from "../../api/request";
import Store from "../../store/Store";


export const formFieldUsername =
    createFormField('用户名', 'username', {
        minLength: 3,
        maxLength: 64,
        type: 'text'
    }, []);

export const attributesPassword: IInputAttributes = {
    minLength: 6,
    maxLength: 128,
    type: 'password'
};

export const formFieldPassword =
    createFormField('密码', 'password', attributesPassword, []);


export const formFieldEmail = (() => {
    const label = '邮箱';
    const regexp = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/;
    const functor = bindValidator.bindRegexp(regexp, `${label}不合法(不支持汉字和大写字母)`);
    return createFormField(label, 'email', {
        minLength: 5,
        maxLength: 128,
        type: 'email'
    }, [functor])
})();

const formFields: IFormField[] = [
    formFieldUsername,
    formFieldPassword,
    formFieldEmail
];

export const apiRegister: IFormApi = {
    to: routeConfig.names.index,
    request: apiRequest.sign.register,
    afterRequest: (data?: FormResult) => {
        if (typeof data !== 'string' && data) {
            Store.store = data;
        }
    }
};


const Register: React.FunctionComponent = () => {
    return <Form title={'注册'}
                 api={apiRegister}
                 leftLink={link.password}
                 rightLink={link.login}
                 formFields={formFields}/>
}
export default Register;
