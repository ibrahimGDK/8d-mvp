import { BrowserRouter, Routes, Route } from "react-router-dom";
import MainLayout from "./components/layout/MainLayout";
import Dashboard from "./pages/Dashboard";
import ProblemDetail from "./pages/ProblemDetail";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route element={<MainLayout />}>
          <Route path="/" element={<Dashboard />} />
          <Route path="/problems/:id" element={<ProblemDetail />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
