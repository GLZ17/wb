import React from 'react';
import Form, {IFormField} from './form/Form';

import link from './form/link';
import {apiRequest} from "../../api/request";

import {
    formFieldUsername,
    formFieldPassword,
    apiRegister
} from './Register';

const apiLogin = {...apiRegister};
apiLogin.request = apiRequest.sign.login;

const formFields: IFormField[] = [
    formFieldUsername,
    formFieldPassword
];


const Login: React.FunctionComponent = () => {
    return <Form title={'登录'}
                 api={apiLogin}
                 leftLink={link.password}
                 rightLink={link.register}
                 formFields={formFields}/>
}
export default Login;
