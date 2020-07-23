import React from 'react';
import {RouteComponentProps} from "react-router-dom";
import Form, {
    IFormField,
    createFormField,
    IFormApi
} from './form/Form';

import link from './form/link';
import {apiRequest} from "../../api/request";
import {FormData} from "../../api/db_table";

import {
    formFieldUsername as appealFormFieldUsername,
    formFieldEmail as appealFormFieldEmail,
    attributesPassword
} from './Register';


interface IState {
    isNextStep: boolean;
}


const formFieldPasswordNew =
    createFormField('新密码', 'password',
        attributesPassword, []);

const formFieldPasswordConfirm =
    createFormField('确认密码', 'pwd',
        attributesPassword, []);

const retrieveFormFieldUsername = {
    ...appealFormFieldUsername,
    attributes: {
        ...appealFormFieldUsername.attributes,
        disabled: true
    }
};
const retrieveFormFieldEmail = {
    ...appealFormFieldEmail,
    attributes: {
        ...appealFormFieldEmail.attributes,
        disabled: true
    }
};

const formFieldsRetrieve: IFormField[] = [
    retrieveFormFieldUsername,
    retrieveFormFieldEmail,
    createFormField('序列号', 'serial', {
        minLength: 1,
        maxLength: 64,
        type: 'text'
    }, []),
    formFieldPasswordNew,
    formFieldPasswordConfirm
];

const formFieldsAppeal: IFormField[] = [
    appealFormFieldUsername,
    appealFormFieldEmail,
];


const initFormApiAppeal = (ob: Password): IFormApi => {
    return {
        request: apiRequest.sign.appeal,
        beforeRequest: (data: FormData) => {
            ob.fillDefaultState(data);
            return [true, ''];
        },
        afterRequest: () => {
            ob.setState({isNextStep: true});
        }
    };
};

const initFormApiRetrieve = (ob: Password): IFormApi => {
    return {
        request: apiRequest.sign.retrieve,
        beforeRequest: (data: FormData) => {
            const status = data[formFieldPasswordNew.fieldName] ===
                data[formFieldPasswordConfirm.fieldName];
            const msg = status ? '' : '两次密码不相等';
            return [status, msg];
        },
        afterRequest: () => {
            ob.props.history.push(link.login.path);
        }
    };
}

class Password extends React.Component<RouteComponentProps, IState> {
    private defaultState: FormData = {};

    public constructor(props: RouteComponentProps) {
        super(props);
        this.state = {isNextStep: false};
    }

    public fillDefaultState = (data: FormData) => {
        this.defaultState = data;
    }

    public render() {
        return this.state.isNextStep
            ? (<Form title={'新密码设置'}
                     api={initFormApiRetrieve(this)}
                     defaultState={this.defaultState}
                     leftLink={link.register}
                     rightLink={link.login}
                     formFields={formFieldsRetrieve}/>)
            : (<Form title={'序列号申请'}
                     api={initFormApiAppeal(this)}
                     defaultState={this.defaultState}
                     leftLink={link.register}
                     rightLink={link.login}
                     formFields={formFieldsAppeal}/>);
    }
}

export default Password;