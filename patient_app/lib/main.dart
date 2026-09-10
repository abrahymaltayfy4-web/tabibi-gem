import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'core/network/api_client.dart';
import 'core/routing/app_router.dart';
import 'core/security/secure_storage_service.dart';
import 'core/theme/app_theme.dart';
import 'features/auth/presentation/cubit/auth_cubit.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  final secureStorage = SecureStorageService();
  final apiClient = ApiClient(dio: Dio(), secureStorage: secureStorage);

  runApp(TabibiPatientApp(
    apiClient: apiClient,
    secureStorage: secureStorage,
  ));
}

class TabibiPatientApp extends StatelessWidget {
  final ApiClient apiClient;
  final SecureStorageService secureStorage;

  const TabibiPatientApp({
    super.key,
    required this.apiClient,
    required this.secureStorage,
  });

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => AuthCubit(
        apiClient: apiClient,
        secureStorage: secureStorage,
      )..checkAuthStatus(),
      child: MaterialApp.router(
        title: 'TABIBI — طبيبي',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        darkTheme: AppTheme.darkTheme,
        themeMode: ThemeMode.system,
        routerConfig: appRouter,
      ),
    );
  }
}
