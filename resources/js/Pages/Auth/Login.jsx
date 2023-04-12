import { useEffect } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    
    // const { data, setData, post, processing, errors, reset } = useForm({
    //     email: '',
    //     password: '',
    //     remember: false,
    // });

    // useEffect(() => {
    //     return () => {
    //         reset('password');
    //     };
    // }, []);

    // const submit = (e) => {
    //     e.preventDefault();

    //     post(route('login'));
    // };

    // return (
    //     <div>

    //     </div>
    // );
}
