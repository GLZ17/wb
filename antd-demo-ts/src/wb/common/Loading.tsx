import React from 'react';
import './loading.css';

interface IProps {
    loading?: boolean;
}

interface IState {
    loading: boolean;
}

class Loading extends React.Component<IProps, IState> {
    private static defaultProps: IProps = {
        loading: false
    }
    private static set = new Map<Loading, Loading>();
    public static start = () => {
        Loading.change(true);
    }
    public static stop = () => {
        Loading.change(false);
    }
    private static change = (loading: boolean) => {
        let flag = true;
        Loading.set.forEach(it => {
            if (flag) {
                flag = false;
                it.change(loading);
            }
        });
    }

    public constructor(props: IProps) {
        super(props);
        this.state = {
            loading: !!props.loading
        };
        Loading.set.set(this, this);
    }

    public change = (loading: boolean) => {
        this.setState({loading});
    }

    public componentWillUnmount() {
        Loading.set.delete(this);
    }

    public render() {
        return this.state.loading &&
            (
                <i className={'lr-overlay df fjc fac'}>
                    <i className={'lr-block'}/>
                </i>
            );
    }
}

export default Loading;

