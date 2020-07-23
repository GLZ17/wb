import React from 'react';
import Form, {
    IFormField,
    createFormField,
    IFormApi
} from '../sign/form/Form';

import link from '../sign/form/link';
import {apiRequest} from "../../api/request";
import {attributesPassword} from '../sign/Register';
import {FormData} from "../../api/db_table";

export const formFieldSeries =
    createFormField('序列号', 'series', {
        minLength: 1,
        maxLength: 64,
        type: 'text'
    }, []);

export const formFieldPasswordNew =
    createFormField('新密码', 'password',
        attributesPassword, []);

export const formFieldPasswordConfirm =
    createFormField('确认密码', 'pwd',
        attributesPassword, []);


const apiReset: IFormApi = {
    to: link.login.path,
    request: apiRequest.sign.login,
    beforeRequest: (data: FormData) => {
        const status = data[formFieldPasswordNew.fieldName] ===
            data[formFieldPasswordConfirm.fieldName];
        const msg = status ? '' : '两次密码不相等';
        return [status, msg];
    }
};
apiReset.request = apiRequest.sign.login;

const formFields: IFormField[] = [
    formFieldSeries,
    formFieldPasswordNew,
    formFieldPasswordConfirm
];


const Reset1: React.FunctionComponent = () => {
    return <Form title={'设置'}
                 api={apiReset}
                 leftLink={link.password}
                 rightLink={link.register}
                 formFields={formFields}/>
}

export default Reset1;