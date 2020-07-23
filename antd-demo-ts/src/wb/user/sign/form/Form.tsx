import React from 'react';
import {Link, withRouter, RouteComponentProps} from "react-router-dom";
import './form.css';
import {ILinkItem} from "./link";
import {Validate, Validator, bindValidator, ValidateResult} from './Validate';
import {IResult, FormData} from "../../../api/db_table";
import Message from "../../../common/Message";
import Loading from "../../../common/Loading";
import {IStoreData} from "../../../store/Store";

export type FormResult = string | IStoreData;

export interface IInputAttributes {
    minLength: number,
    maxLength: number;
    type?: string;
    disabled?: boolean;
}

export interface IFormField {
    fieldName: string;
    label: string;
    attributes: IInputAttributes;
    validators: Validator[];
}

export interface IFormApi {
    request: (data: { [key: string]: string }) => Promise<IResult<FormResult>>
    to?: string;
    afterRequest?: (data?: FormResult) => any;
    beforeRequest?: (data: FormData) => ValidateResult;
}

interface IProps extends RouteComponentProps {
    title: string;
    leftLink: ILinkItem;
    rightLink: ILinkItem;
    api: IFormApi;
    formFields: IFormField[];
    defaultState?: FormData
}

export const createFormField = (label: string,
                                fieldName: string,
                                attributes: IInputAttributes,
                                validators: Validator[]): IFormField => {
    return {
        label,
        fieldName,
        attributes,
        validators: [
            bindValidator.bindRequired(true, `${label}不能为空`),
            bindValidator.bindMinLength(attributes.minLength, `${label}的字个数不少于${attributes.minLength}`),
            bindValidator.bindMaxLength(attributes.maxLength, `${label}的字个数不多于${attributes.maxLength}`),
            ...validators
        ]
    }
}

interface IState extends FormData {
    errorMessage: string;
}

type IValidateResult = [boolean, IState];

class Form extends React.Component<IProps, IState> {
    public constructor(props: IProps) {
        super(props);
        const defaultState = this.props.defaultState
            ? this.props.defaultState : {};
        this.state = this.retrieveInitState();
        this.state = {
            ...this.retrieveInitState(),
            ...defaultState
        }
    }

    private retrieveInitState = (): IState => {
        const state: IState = {errorMessage: ''};
        this.props.formFields.forEach(it => {
            state[it.fieldName] = '';
        });
        return state;
    }
    private handleReset = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        this.setState(this.retrieveInitState());
    }

    private handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const [status, state] = this.validate();
        this.setState(state);
        if (status) {
            this.request(state).then().catch();
        }
    }
    private validate = (): IValidateResult => {
        const res: IValidateResult = [true, {errorMessage: ''}];
        this.props.formFields.forEach(it => {
            const value: string = this.state[it.fieldName].trim();
            res[1][it.fieldName] = value;
            if (res[0]) {
                const [status, message] = Validate(value, it.validators);
                res[0] = status;
                res[1].errorMessage = message;
            }
        });
        if (res[0] && this.props.api.beforeRequest) {
            const [status, message] = this.props.api.beforeRequest(res[1]);
            res[0] = status;
            res[1].errorMessage = message;
        }
        return res;
    }
    private request = async (state: IState) => {
        const {errorMessage, ...rest} = state;
        Loading.start();
        const res = await this.props.api.request({...rest});
        Loading.stop();
        Message.message(res);
        if (res.status) {
            if (this.props.api.afterRequest) {
                this.props.api.afterRequest(res.data);
            }
            if (this.props.api.to) {
                this.props.history.push(this.props.api.to);
            }
        }
    }
    private bindHandleBlur = (formField: IFormField) => {
        return () => {
            const {fieldName, validators} = formField;
            const res = Validate(this.state[fieldName], validators);
            this.setState({errorMessage: res[1]});
        }
    }

    private bindHandleChange = (formField: IFormField) => {
        return (e: React.ChangeEvent<HTMLInputElement>) => {
            this.setState({
                [formField.fieldName]: e.currentTarget.value,
                errorMessage: ''
            });
        }
    }

    public render() {
        const {title, leftLink, rightLink} = this.props;
        return (
            <div>
                <div className={'ur-page'}>
                    <h3 className={'tac ur-title'}>{title}</h3>
                    <form onReset={this.handleReset}
                          onSubmit={this.handleSubmit}>
                        {
                            this.props.formFields.map((it, ix) => {
                                const {fieldName, label, attributes} = it;
                                return (
                                    <p className={'ur-it'} key={`${ix}--${fieldName}`}>
                                        <label htmlFor={fieldName}>{label}</label>
                                        <input id={fieldName}
                                               name={fieldName}
                                               value={this.state[fieldName]}
                                               onBlur={this.bindHandleBlur(it)}
                                               onChange={this.bindHandleChange(it)}
                                               {...attributes}/>
                                    </p>
                                );
                            })
                        }
                        <p className={'error-msg tac'}>{this.state.errorMessage}</p>
                        <div className={'ur-bb'}>
                            <input type={'reset'} value={'清空'}/>
                            <input type={'submit'} value={'提交'}/>
                        </div>
                    </form>
                    <div className={'ur-link'}>
                        <Link to={leftLink.path} className={'tar'}>
                            {leftLink.text}
                        </Link>
                        <Link to={rightLink.path} className={'tal'}>
                            {rightLink.text}
                        </Link>
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(Form);