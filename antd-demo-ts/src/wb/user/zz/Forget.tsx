import React from 'react';
import Form, {IFormField, IFormApi} from '../sign/form/Form';

import link from '../sign/form/link';
import {apiRequest} from "../../api/request";

import {
    formFieldUsername,
    formFieldEmail,
} from '../sign/Register';

const apiForget : IFormApi= {
    to : link.password.path,
    request : apiRequest.sign.appeal
};


const formFields: IFormField[] = [
    formFieldUsername,
    formFieldEmail
];


const Forget1: React.FunctionComponent = () => {
    return <Form title={'申请'}
                 api={apiForget}
                 leftLink={link.password}
                 rightLink={link.login}
                 formFields={formFields}/>
}

export default Forget1;