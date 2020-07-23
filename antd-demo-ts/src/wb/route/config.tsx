import {createBrowserHistory} from 'history';

const names = {
    index: '/',
    practise: '/practise',
    curve: '/curve',
    login: '/login',
    register: '/register',
    password: '/password',
};


interface IPath {
    id: number;
    path: string;
    name: keyof typeof names;
}

const paths: IPath[] = [
    {
        id: 1,
        name: 'practise',
        path: names.practise
    },
    {
        id: 2,
        name: 'curve',
        path: names.curve
    }
];

const routeConfig = {
    names: names,
    paths: paths,
    history: {
        push: (path: string) => {
            createBrowserHistory().push(path);
        }
    }
}

export {routeConfig};