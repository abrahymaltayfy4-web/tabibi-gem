import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../core/network/api_client.dart';
import '../../../../core/security/secure_storage_service.dart';
import '../../domain/entities/patient_user.dart';
import 'auth_state.dart';

class AuthCubit extends Cubit<AuthState> {
  final ApiClient apiClient;
  final SecureStorageService secureStorage;

  AuthCubit({
    required this.apiClient,
    required this.secureStorage,
  }) : super(AuthInitial());

  String _extractErrorMessage(Object error, String fallbackMessage) {
    if (error is DioException) {
      if (error.response?.data != null && error.response?.data is Map) {
        final data = error.response!.data as Map;
        if (data.containsKey('errors') && data['errors'] is Map) {
          final errors = data['errors'] as Map;
          for (final value in errors.values) {
            if (value is List && value.isNotEmpty) {
              return value.first.toString();
            }
          }
        }
        if (data.containsKey('message') && data['message'] != null) {
          return data['message'].toString();
        }
      }
      if (error.type == DioExceptionType.connectionTimeout ||
          error.type == DioExceptionType.receiveTimeout ||
          error.type == DioExceptionType.connectionError) {
        return 'تعذر الاتصال بالخادم، يرجى التأكد من تشغيل الباك إند (php artisan serve)';
      }
    }
    return fallbackMessage;
  }

  Future<void> checkAuthStatus() async {
    emit(AuthLoading());
    try {
      final token = await secureStorage.getToken();
      if (token != null && token.isNotEmpty) {
        final response = await apiClient.get('auth/me');
        if (response.statusCode == 200 && response.data['success'] == true) {
          final userData = response.data['data'];
          final user = PatientUser(
            id: userData['id'],
            uuid: userData['uuid'],
            fullName: userData['full_name'],
            phone: userData['phone'],
            email: userData['email'],
            token: token,
          );
          emit(Authenticated(user));
          return;
        }
      }
      emit(Unauthenticated());
    } catch (e) {
      emit(Unauthenticated());
    }
  }

  Future<void> login(String phoneOrEmail, String password) async {
    emit(AuthLoading());
    try {
      final response = await apiClient.post('auth/login', data: {
        'phone_or_email': phoneOrEmail,
        'password': password,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final token = data['token'];
        final userData = data['user'];

        await secureStorage.saveToken(token);

        final user = PatientUser(
          id: userData['id'],
          uuid: userData['uuid'],
          fullName: userData['full_name'],
          phone: userData['phone'],
          email: userData['email'],
          token: token,
        );

        emit(Authenticated(user));
      } else {
        final msg = response.data['message'] ?? 'فشل تسجيل الدخول';
        emit(AuthFailureState(msg));
      }
    } catch (e) {
      emit(AuthFailureState(_extractErrorMessage(e, 'بيانات الدخول غير صحيحة أو خطأ في الاتصال')));
    }
  }

  Future<void> registerPatient({
    required String fullName,
    required String phone,
    required String password,
    String? email,
    String? gender,
    String? bloodGroup,
  }) async {
    emit(AuthLoading());
    try {
      final response = await apiClient.post('auth/register/patient', data: {
        'full_name': fullName,
        'phone': phone,
        'email': email,
        'password': password,
        'gender': gender ?? 'Male',
        'blood_group': bloodGroup ?? 'O+',
      });

      if (response.statusCode == 201 && response.data['success'] == true) {
        final data = response.data['data'];
        final token = data['token'];
        final userData = data['user'];

        await secureStorage.saveToken(token);

        final user = PatientUser(
          id: userData['id'],
          uuid: userData['uuid'],
          fullName: userData['full_name'],
          phone: userData['phone'],
          email: userData['email'],
          token: token,
        );

        emit(Authenticated(user));
      } else {
        final msg = response.data['message'] ?? 'فشل إنشاء الحساب';
        emit(AuthFailureState(msg));
      }
    } catch (e) {
      emit(AuthFailureState(_extractErrorMessage(e, 'رقم الهاتف مسجل مسبقاً أو هناك خطأ في البيانات')));
    }
  }

  Future<void> logout() async {
    try {
      await apiClient.post('auth/logout');
    } catch (_) {}
    await secureStorage.clearAll();
    emit(Unauthenticated());
  }
}

