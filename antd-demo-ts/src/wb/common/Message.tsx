import React from 'react';
import './mesage.css';
import {IResult} from "../api/db_table";

interface IMessageItem {
    msg: string;
    clsName: string;
    id: number;
}

interface IState {
    messages: IMessageItem[];
}

type handleMessage = (type: 'me-success' | 'me-failure', msg: string) => any;


class Message extends React.Component<{}, IState> {
    private static set = new Map<Message, Message>();
    private amount: number = 0;

    public static message = (result: IResult<any>) => {
        if (result.status) {
            Message.success(result.message);
        } else {
            Message.failure(result.message);
        }
    }

    public static success = (message: string) => {
        Message.show("me-success", message);
    }
    public static failure = (message: string) => {
        Message.show("me-failure", message);
    }
    private static show: handleMessage = (type, msg) => {
        let flag = true;
        Message.set.forEach(it => {
            if (flag) {
                flag = false;
                it.message(type, msg);
            }
        });
    }

    public constructor(props: {}) {
        super(props);
        this.state = {
            messages: []
        }
        Message.set.set(this, this);
    }

    public componentWillUnmount() {
        Message.set.delete(this);
    }


    private bindRemoveItem = (id: number) => {
        return () => {
            this.setState({
                messages: this.state.messages.filter(it => it.id !== id)
            })
        }
    }
    private message: handleMessage = (clsName, msg) => {
        const item: IMessageItem = {
            id: ++this.amount,
            clsName,
            msg
        };
        this.setState({
            messages: [...this.state.messages, item]
        })
    }

    public render() {
        return (
            <ul className={'me-page pf t0'}>
                {this.state.messages.map(it => {
                    const cls = `${it.clsName} me-item tac`;
                    return (<li key={it.id}
                                onAnimationEnd={this.bindRemoveItem(it.id)}
                                className={cls}>
                        {it.msg}
                    </li>)
                })}
            </ul>
        );
    }
}

export default Message;