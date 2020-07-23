import '../assets/css/reset.css';
import '../assets/css/index.css';
import '../assets/css/common.css';
import './index.css';

import React from 'react';
import Loading from "./common/Loading";
import Message from "./common/Message";
import Routes from './route/Index';
import {apiRequest} from "./api/request";
import Store from "./store/Store";

interface IState {
    show: boolean;
}

class Index extends React.Component<{}, IState> {
    private unMounted = false;
    public constructor(props: {}) {
        super(props);
        this.state = {
            show: false
        };
    }

    public showPage = () => {
        if (!this.unMounted) {
            this.setState({show: true});
        }
    }

    public async componentDidMount() {
        if (Store.store.token) {
            Loading.start();
            const res = await apiRequest.sign.login();
            Loading.stop();
            if (res.code) {
                Message.message(res);
            }
            this.showPage();
        } else {
            this.showPage();
        }
    }

    public componentWillUnmount() {
        this.unMounted = true;
    }

    public render() {
        const {show} = this.state;
        return (
            <React.Fragment>
                <Message/>
                <Loading/>
                {show && <Routes/>}
            </React.Fragment>
        )
    };
}

export default Index;